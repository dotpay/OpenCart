<?php

class ControllerPaymentDotpay extends Controller {

    const REQUEST_URL = 'https://ssl.dotpay.pl/test_payment/';
    const URL = 'index.php?route=payment/dotpay/callback';
    const URLC = 'index.php?route=payment/dotpay/confirmation';   
    const IP_ADDRESS = '195.150.9.37'; 
    const REQUEST_METHOD = 'POST';
    const API_VERSION = 'dev';         
    const TYPE = '0'; 

    private $error = array();
    private $settings = array();

    public function index() {
        
        $this->load->language('payment/dotpay');        
        $this->load->model('setting/setting');
        $this->load->model('localisation/currency');
        $this->load->model('localisation/order_status');
        $this->document->setTitle($this->language->get('heading_title'));

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
            $this->load->model('setting/setting');
            $this->model_setting_setting->editSetting('dotpay', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'href' => HTTPS_SERVER . 'index.php?route=common/home&token=' . $this->session->data['token'],
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        );

        $data['breadcrumbs'][] = array(
            'href' => HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token'],
            'text' => $this->language->get('text_payment'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'href' => HTTPS_SERVER . 'index.php?route=payment/dotpay&token=' . $this->session->data['token'],
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_edit'] = $this->language->get('text_edit');

        $data['text_active_status'] = $this->language->get('text_active_status');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_sort_order'] = $this->language->get('text_sort_order');

        $data['text_dotpay_id'] = $this->language->get('text_dotpay_id');
        $data['text_dotpay_request_url'] = $this->language->get('text_dotpay_request_url');
        $data['text_dotpay_URLC'] = $this->language->get('text_dotpay_URLC');
        $data['text_dotpay_ip'] = $this->language->get('text_dotpay_ip');
        $data['text_dotpay_pin'] = $this->language->get('text_dotpay_pin');
        $data['text_dotpay_pin_help'] = $this->language->get('text_dotpay_pin_help');
        $data['text_dotpay_currency'] = $this->language->get('text_dotpay_currency');        
        $data['text_dotpay_status_rejected'] = $this->language->get('text_dotpay_status_rejected');
        $data['text_dotpay_status_completed'] = $this->language->get('text_dotpay_status_completed');
        $data['text_dotpay_status_processing'] = $this->language->get('text_dotpay_status_processing');


        $data['dotpay_status'] = (isset($this->request->post['dotpay_status']) ? $this->request->post['dotpay_status'] : $this->config->get('dotpay_status'));
        $data['dotpay_sort_order'] = (isset($this->request->post['dotpay_sort_order']) ? $this->request->post['dotpay_sort_order'] : $this->config->get('dotpay_sort_order'));

        $data['dotpay_id'] = (isset($this->request->post['dotpay_id']) ? $this->request->post['dotpay_id'] : $this->config->get('dotpay_id'));
        $data['dotpay_ip'] = (isset($this->request->post['dotpay_ip']) ? $this->request->post['dotpay_ip'] : $this->config->get('dotpay_ip'));
        $data['dotpay_request_url'] = (isset($this->request->post['dotpay_request_url']) ? $this->request->post['dotpay_request_url'] : $this->config->get('dotpay_request_url'));
        $data['dotpay_URLC'] = (isset($this->request->post['dotpay_URLC']) ? $this->request->post['dotpay_URLC'] : $this->config->get('dotpay_URLC'));
        $data['dotpay_pin'] = (isset($this->request->post['dotpay_pin']) ? $this->request->post['dotpay_pin'] : $this->config->get('dotpay_pin'));
        $data['dotpay_currency'] = (isset($this->request->post['dotpay_currency']) ? $this->request->post['dotpay_currency'] : $this->config->get('dotpay_currency'));
        $data['dotpay_status_completed'] = (isset($this->request->post['dotpay_status_completed']) ? $this->request->post['dotpay_status_completed'] : $this->config->get('dotpay_status_completed'));
        $data['dotpay_status_rejected'] = (isset($this->request->post['dotpay_status_rejected']) ? $this->request->post['dotpay_status_rejected'] : $this->config->get('dotpay_status_rejected'));
        $data['dotpay_status_processing'] = (isset($this->request->post['dotpay_status_processing']) ? $this->request->post['dotpay_status_processing'] : $this->config->get('dotpay_status_processing'));
        
                
        $data['dotpay_request_method'] = $this->config->get('dotpay_request_method');
        $data['dotpay_api_version'] = $this->config->get('dotpay_api_version');        
        $data['dotpay_URL'] = $this->config->get('dotpay_URL');
        $data['dotpay_type'] = $this->config->get('dotpay_type');        

      
        $data['currencies'] =  $this->model_localisation_currency->getCurrencies();
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        
      
        $data['error'] = (!empty($this->error) ? $this->error : null);
        $data['action'] = HTTPS_SERVER . 'index.php?route=payment/dotpay&token=' . $this->session->data['token'];
        $data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token'];
        
        $data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_edit'] = $this->language->get('button_edit');
       
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        
        $this->response->setOutput($this->load->view('payment/dotpay.tpl', $data));
    }

    public function install() {

        $this->load->model('setting/setting');

        $this->settings = array(
            'dotpay_request_url' => self::REQUEST_URL,
            'dotpay_request_method' => self::REQUEST_METHOD,
            'dotpay_api_version' => self::API_VERSION,
            'dotpay_ip' => self::IP_ADDRESS,            
            'dotpay_URL' => self::URL,
            'dotpay_URLC' => self::URLC,
            'dotpay_type' => self::TYPE,
            'dotpay_currency' => $this->config->get('config_currency'),
        );

        $this->model_setting_setting->editSetting('dotpay', $this->settings);
    }

    public function uninstall() {

        $this->load->model('setting/setting');

        $this->model_setting_setting->deleteSetting('dotpay');
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'payment/dotpay'))
            $this->error['permission'] = $this->language->get('error_permission');
        if (!$this->request->post['dotpay_id'])
            $this->error['dotpay_id'] = $this->language->get('error_dotpay_id');
        if (!$this->request->post['dotpay_pin'])
            $this->error['dotpay_pin'] = $this->language->get('error_dotpay_pin');
        return (!$this->error ? true : false);
    }

}

?>