<?php

class ControllerPaymentDotpay extends Controller
{

    private $error = array();

    public function index()
    {
        $this->load->language('payment/dotpay');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate())
        {
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
        
        $data['text_sort_order'] = $this->language->get('text_sort_order');
        $data['text_dotpay_id'] = $this->language->get('text_dotpay_id');
        $data['text_dotpay_ip'] = $this->language->get('text_dotpay_ip');
        $data['text_dotpay_currency'] = $this->language->get('text_dotpay_currency');
      

//        $data['entry_transferuj_status'] = $this->language->get('entry_transferuj_status');
//        $data['entry_transferuj_status_yes'] = $this->language->get('entry_transferuj_status_yes');
//        $data['entry_transferuj_status_no'] = $this->language->get('entry_transferuj_status_no');
//        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
//        $data['entry_transferuj_ip'] = $this->language->get('entry_transferuj_ip');

//        $data['entry_transferuj_conf_code'] = $this->language->get('entry_transferuj_conf_code');
//        $data['entry_transferuj_conf_code_hint'] = $this->language->get('entry_transferuj_conf_code_hint');
//
//        $data['entry_settings_orders'] = $this->language->get('entry_settings_orders');
//        $data['entry_transferuj_order_status_error'] = $this->language->get('entry_transferuj_order_status_error');
//        $data['entry_transferuj_order_status_completed'] = $this->language->get('entry_transferuj_order_status_completed');

//        $data['button_save'] = $this->language->get('button_save');
//        $data['button_cancel'] = $this->language->get('button_cancel');
//        $data['tab_general'] = $this->language->get('tab_general');
//
       
//
//        $data['entry_view_settings'] = $this->language->get('entry_view_settings');
//        $data['entry_transferuj_payment_place'] = $this->language->get('entry_transferuj_payment_place');
//        $data['entry_transferuj_payment_place_0'] = $this->language->get('entry_transferuj_payment_place_0');
//        $data['entry_transferuj_payment_place_1'] = $this->language->get('entry_transferuj_payment_place_1');
//
//        $data['entry_transferuj_payment_view'] = $this->language->get('entry_transferuj_payment_view');
//        $data['entry_transferuj_payment_view_0'] = $this->language->get('entry_transferuj_payment_view_0');
//        $data['entry_transferuj_payment_view_1'] = $this->language->get('entry_transferuj_payment_view_1');

//        $data['transferuj_status'] = (isset($this->request->post['transferuj_status']) ? $this->request->post['transferuj_status'] : $this->config->get('transferuj_status'));
//        $data['transferuj_sort_order'] = (isset($this->request->post['transferuj_sort_order']) ? $this->request->post['transferuj_sort_order'] : $this->config->get('transferuj_sort_order'));
//        $data['transferuj_conf_code'] = (isset($this->request->post['transferuj_conf_code']) ? $this->request->post['transferuj_conf_code'] : $this->config->get('transferuj_conf_code'));
//        $data['transferuj_payment_place'] = (isset($this->request->post['transferuj_payment_place']) ? $this->request->post['transferuj_payment_place'] : $this->config->get('transferuj_payment_place'));
//        $data['transferuj_payment_view'] = (isset($this->request->post['transferuj_payment_view']) ? $this->request->post['transferuj_payment_view'] : $this->config->get('transferuj_payment_view'));

        

        $data['dotpay_sort_order'] = (isset($this->request->post['dotpay_sort_order']) ? $this->request->post['dotpay_sort_order'] : $this->config->get('dotpay_sort_order'));
        $data['dotpay_id'] = (isset($this->request->post['dotpay_id']) ? $this->request->post['dotpay_id'] : $this->config->get('dotpay_id'));
        $data['dotpay_ip'] = (isset($this->request->post['dotpay_ip']) ? $this->request->post['dotpay_ip'] : (!empty($this->config->get('dotpay_ip')) ? $this->config->get('dotpay_ip') : '195.15.09.37'));
        
        
        
        
        
        
        $this->load->model('localisation/currency');

        if (!empty($currency_info))
        {
            $data['curr'][] = $currency_info['code'];
        }
        $currency_info = $this->model_localisation_currency->getCurrency('0');
        if (!empty($currency_info))
        {
            $data['curr'][] = $currency_info['code'];
        }
        $currency_info = $this->model_localisation_currency->getCurrency('1');
        if (!empty($currency_info))
        {
            $data['curr'][] = $currency_info['code'];
        }
        $currency_info = $this->model_localisation_currency->getCurrency('2');
        if (!empty($currency_info))
        {
            $data['curr'][] = $currency_info['code'];
        }
        $currency_info = $this->model_localisation_currency->getCurrency('3');
        if (!empty($currency_info))
        {
            $data['curr'][] = $currency_info['code'];
        }
        $currency_info = $this->model_localisation_currency->getCurrency('4');
        if (!empty($currency_info))
        {
            $data['curr'][] = $currency_info['code'];
        }
        $currency_info = $this->model_localisation_currency->getCurrency('5');
        if (!empty($currency_info))
        {
            $data['curr'][] = $currency_info['code'];
        }
        $currency_info = $this->model_localisation_currency->getCurrency('6');
        if (!empty($currency_info))
        {
            $data['curr'][] = $currency_info['code'];
        }
        $currency_info = $this->model_localisation_currency->getCurrency('7');
        if (!empty($currency_info))
        {
            $data['curr'][] = $currency_info['code'];
        }

        $data['dotpay_currency'] = (isset($this->request->post['dotpay_currency']) ? $this->request->post['dotpay_currency'] : $this->config->get('dotpay_currency'));

        
        
        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        $data['transferuj_order_status_error'] = (isset($this->request->post['transferuj_order_status_error']) ? $this->request->post['transferuj_order_status_error'] : $this->config->get('transferuj_order_status_error'));
        $data['transferuj_order_status_completed'] = (isset($this->request->post['transferuj_order_status_completed']) ? $this->request->post['transferuj_order_status_completed'] : $this->config->get('transferuj_order_status_completed'));

                
        $data['error_warning'] = (isset($this->error['warning']) ? $this->error['warning'] : '');
        $data['error_merchant'] = (isset($this->error['merchant']) ? $this->error['merchant'] : '');
        $data['error_password'] = (isset($this->error['password']) ? $this->error['password'] : '');
        
        $data['action'] = HTTPS_SERVER . 'index.php?route=payment/dotpay&token=' . $this->session->data['token'];
        $data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token'];
        
        
        $this->template = 'payment/transferuj.tpl';
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('payment/dotpay.tpl', $data));
    }
    
    public function install() {
//		$this->load->model('payment/dotpay');
//
//		$this->load->model('setting/setting');
//
//		$this->model_payment_amazon_checkout->install();
//		
//		$this->model_setting_setting->editSetting('amazon_checkout', $this->settings);
	}

	public function uninstall() {
//		$this->load->model('payment/amazon_checkout');
//
//		$this->model_payment_amazon_checkout->uninstall();
	}

    private function validate()
    {
        if (!$this->user->hasPermission('modify', 'payment/dotpay'))
            $this->error['warning'] = $this->language->get('error_permission');
        if (!$this->request->post['dotpay_id'])
            $this->error['merchant'] = $this->language->get('error_merchant');
        return (!$this->error ? true : false);
    }

}

?>