<?php 

/**
 * Admin controller
 */
class ControllerPaymentDotpayNew extends Controller {
    /**
     * Name of plugin
     */
    const PLUGIN_NAME = 'dotpay_new';
    
    /**
     * Test payment url for developers
     */
    const DEV_PAYMENT_URL = 'https://ssl.dotpay.pl/test_payment/';
    
    /**
     * Production payment url for sellers
     */
    const PROD_PAYMENT_URL = 'https://ssl.dotpay.pl/t2/';
    
    /**
     * Test url of seller API for developers
     */
    const DEV_SELLER_URL = 'https://ssl.dotpay.pl/test_seller/';
    
    /**
     * Production url of seller API for sellers
     */
    const PROD_SELLER_URL = 'https://ssl.dotpay.pl/s2/login/';
    
    /**
     * Url of shop site for return
     */
    const URL = 'index.php?route=payment/dotpay_new/back';
    
    /**
     * Url of shop site for URLC confirmation
     */
    const URLC = 'index.php?route=payment/dotpay_new/confirm';
    
    /**
     * IP of Dotpay server confirmation
     */
    const DOTPAY_IP = '195.150.9.37';
    
    /**
     * IP of Dotpay office
     */
    const OFFICE_IP = '77.79.195.34';
    
    /**
     * Version of API Dotpay
     */
    const API_VERSION = 'dev';
    
    /**
     * Version of payment plugin
     */
    const VERSION = '2.0.0';
    
    /**
     * @var array List of errors
     */
    private $error = array();
    
    /**
     * @var array List of settings
     */
    private $settings = array();
    
    /**
     * Constructor of admin controller
     * @param Registry $registry Registry object of OpenCart
     */
    public function __construct($registry){
        parent::__construct($registry);
        $this->settings = array(
            self::PLUGIN_NAME.'_dev_payment_url' => self::DEV_PAYMENT_URL,
            self::PLUGIN_NAME.'_prod_payment_url' => self::PROD_PAYMENT_URL,
            self::PLUGIN_NAME.'_target_payment_url' => self::PROD_PAYMENT_URL,
            self::PLUGIN_NAME.'_dev_seller_url' => self::DEV_SELLER_URL,
            self::PLUGIN_NAME.'_prod_seller_url' => self::PROD_SELLER_URL,
            self::PLUGIN_NAME.'_target_seller_url' => self::PROD_SELLER_URL,
            self::PLUGIN_NAME.'_sort_order' => 1,
            self::PLUGIN_NAME.'_api_version' => self::API_VERSION,
            self::PLUGIN_NAME.'_plugin_version' => self::VERSION,
            self::PLUGIN_NAME.'_ip' => self::DOTPAY_IP,
            self::PLUGIN_NAME.'_office_ip' => self::OFFICE_IP,
            self::PLUGIN_NAME.'_URL' => self::URL,
            self::PLUGIN_NAME.'_URLC' => self::URLC,
            self::PLUGIN_NAME.'_status' => 0,
            self::PLUGIN_NAME.'_id' => '',
            self::PLUGIN_NAME.'_pin' => '',
            self::PLUGIN_NAME.'_test' => 0,
            self::PLUGIN_NAME.'_username' => '',
            self::PLUGIN_NAME.'_password' => '',
            self::PLUGIN_NAME.'_oc' => 0,
            self::PLUGIN_NAME.'_pv' => 0,
            self::PLUGIN_NAME.'_pv_id' => '',
            self::PLUGIN_NAME.'_pv_pin' => '',
            self::PLUGIN_NAME.'_pv_curr' => '',
            self::PLUGIN_NAME.'_cc' => 0,
            self::PLUGIN_NAME.'_mp' => 0,
            self::PLUGIN_NAME.'_blik' => 0,
            self::PLUGIN_NAME.'_widget' => 1,
            self::PLUGIN_NAME.'_status_completed' => 5,
            self::PLUGIN_NAME.'_status_rejected' => 7,
            self::PLUGIN_NAME.'_status_processing' => 2,
            self::PLUGIN_NAME.'_status_return' => 3
        );
    }
    
    /**
     * Returns full path of Dotpay payment extension
     * @return string
     */
    private function getName() {
        if(strpos($this->request->get['route'], 'dotpay') !== false)
            return $this->request->get['route'];
        else if(version_compare(VERSION, '2.2', '>='))
            return 'payment/'.self::PLUGIN_NAME;
        else
            return 'payment/'.self::PLUGIN_NAME;
    }
    
    /**
     * Default action, called on settings page
     */
    public function index() {
        $this->load->language('payment/'.self::PLUGIN_NAME);
        $this->load->model('setting/setting');
        $this->load->model('localisation/order_status');
        $this->load->model('localisation/return_status');
        $this->load->model(dirname($this->getName()).'/dotpay_oc');
        
        $this->document->setTitle($this->language->get('heading_title'));
        
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
            $this->load->model('setting/setting');
            foreach ($this->settings as $key => $value) {
                if(isset($this->request->post[$key]))
                    $this->settings[$key] = $this->request->post[$key];
            }
            if($this->request->post[self::PLUGIN_NAME.'_test'] == 1) {
                $this->settings[self::PLUGIN_NAME.'_target_payment_url'] = self::DEV_PAYMENT_URL;
                $this->settings[self::PLUGIN_NAME.'_target_seller_url'] = self::DEV_SELLER_URL;
            } else {
                $this->settings[self::PLUGIN_NAME.'_target_payment_url'] = self::PROD_PAYMENT_URL;
                $this->settings[self::PLUGIN_NAME.'_target_seller_url'] = self::PROD_SELLER_URL;
            }
            $this->model_setting_setting->editSetting(self::PLUGIN_NAME, $this->settings);
            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link($this->getName(), 'token=' . $this->session->data['token'], 'SSL'));
        }
        
        if (isset($this->session->data['success'])) {
            $data['success_msg'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success_msg'] = '';
        }
        
        $this->document->addStyle('view/stylesheet/dotpay/datatables.css');
        $this->document->addScript('view/javascript/dotpay/datatables.js');
        
        $data['plugin_name'] = self::PLUGIN_NAME;
        $data['base_url'] = dirname(HTTPS_SERVER).'/';
        $data['datatable_language'] = ($this->language->get('code')=='pl')?'Polish':'English';
        
        $data['action'] = HTTPS_SERVER . 'index.php?route='.$this->getName().'&token=' . $this->session->data['token'];
        $data['cancel'] = HTTPS_SERVER . 'index.php?route=extension&type=payment&token=' . $this->session->data['token'];
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        
        $data['heading_title'] = $this->language->get('heading_title');
        $data['breadcrumbs'] = $this->getBreadcrumbs();
        
        $data['main_header_edit'] = $this->language->get('main_header_edit');
        $data['tab_main'] = $this->language->get('tab_main');
        $data['tab_channels'] = $this->language->get('tab_channels');
        $data['tab_statuses'] = $this->language->get('tab_statuses');
        $data['tab_env'] = $this->language->get('tab_env');
        $data['tab_cards'] = $this->language->get('tab_cards');
        
        $data['text_dotpay_register'] = $this->language->get('text_dotpay_register');
        
        $data['text_active_status'] = $this->language->get('text_active_status');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        
        $data['text_dotpay_id'] = $this->language->get('text_dotpay_id');
        $data['text_dotpay_id_help'] = $this->language->get('text_dotpay_id_help');
        $data['text_dotpay_id_validate'] = $this->language->get('text_dotpay_id_validate');
        $data['text_dotpay_pin'] = $this->language->get('text_dotpay_pin');
        $data['text_dotpay_pin_help'] = $this->language->get('text_dotpay_pin_help');
        $data['text_dotpay_pin_validate'] = $this->language->get('text_dotpay_pin_validate');
        $data['text_dotpay_test'] = $this->language->get('text_dotpay_test');
        $data['text_sort_order'] = $this->language->get('text_sort_order');
        $data['text_dotpay_username'] = $this->language->get('text_dotpay_username');
        $data['text_dotpay_password'] = $this->language->get('text_dotpay_password');
        
        $data['text_dotpay_oc'] = $this->language->get('text_dotpay_oc');
        $data['text_dotpay_pv'] = $this->language->get('text_dotpay_pv');
        $data['text_dotpay_pv_id'] = $this->language->get('text_dotpay_pv_id');
        $data['text_dotpay_pv_pin'] = $this->language->get('text_dotpay_pv_pin');
        $data['text_dotpay_pv_curr'] = $this->language->get('text_dotpay_pv_curr');
        $data['text_dotpay_pv_curr_help'] = $this->language->get('text_dotpay_pv_curr_help');
        $data['text_dotpay_cc'] = $this->language->get('text_dotpay_cc');
        $data['text_dotpay_mp'] = $this->language->get('text_dotpay_mp');
        $data['text_dotpay_blik'] = $this->language->get('text_dotpay_blik');
        $data['text_dotpay_widget'] = $this->language->get('text_dotpay_widget');
		
        $data['text_dotpay_test_info'] = $this->language->get('text_dotpay_test_info');
        $data['text_dotpay_api_info'] = $this->language->get('text_dotpay_api_info');
        
        $data['text_dotpay_status_rejected'] = $this->language->get('text_dotpay_status_rejected');
        $data['text_dotpay_status_rejected_2'] = $this->language->get('text_dotpay_status_rejected_2');
        $data['text_dotpay_status_completed'] = $this->language->get('text_dotpay_status_completed');
        $data['text_dotpay_status_completed_2'] = $this->language->get('text_dotpay_status_completed_2');
        $data['text_dotpay_status_processing'] = $this->language->get('text_dotpay_status_processing');
        $data['text_dotpay_status_processing_2'] = $this->language->get('text_dotpay_status_processing_2');
        $data['text_dotpay_status_return'] = $this->language->get('text_dotpay_status_return');
        
        $data['text_dotpay_plugin_version'] = $this->language->get('text_dotpay_plugin_version');
        $data['text_dotpay_plugin_version_check'] = $this->language->get('text_dotpay_plugin_version_check');
        $data['text_dotpay_api_version'] = $this->language->get('text_dotpay_api_version');
        $data['text_dotpay_URL'] = $this->language->get('text_dotpay_URL');
        $data['text_dotpay_URLC'] = $this->language->get('text_dotpay_URLC');
        $data['text_dotpay_ip'] = $this->language->get('text_dotpay_ip');
        $data['text_dotpay_office_ip'] = $this->language->get('text_dotpay_office_ip');
        
        $data['ocmanage_card_number'] = $this->language->get('ocmanage_card_number');
        $data['ocmanage_card_brand'] = $this->language->get('ocmanage_card_brand');
        $data['ocmanage_username'] = $this->language->get('ocmanage_username');
        $data['ocmanage_email'] = $this->language->get('ocmanage_email');
        $data['ocmanage_register_date'] = $this->language->get('ocmanage_register_date');
        $data['ocmanage_deregister'] = $this->language->get('ocmanage_deregister');
        $data['ocmanage_deregister_card'] = $this->language->get('ocmanage_deregister_card');
        $data['ocmanage_on_remove_message'] = $this->language->get('ocmanage_on_remove_message');
        $data['ocmanage_on_done_message'] = $this->language->get('ocmanage_on_done_message');
        $data['ocmanage_on_failure_message'] = $this->language->get('ocmanage_on_failure_message');
        $data['ocmanage_remove_url'] = HTTPS_SERVER . 'index.php?route='.$this->getName().'/ocremove&token='.$this->session->data['token'];
        
        $data['dotpay_status'] = (isset($this->request->post[self::PLUGIN_NAME.'_status']) ? $this->request->post[self::PLUGIN_NAME.'_status'] : $this->config->get(self::PLUGIN_NAME.'_status'));
        $data['dotpay_id'] = $this->config->get(self::PLUGIN_NAME.'_id');
        $data['dotpay_pin'] = $this->config->get(self::PLUGIN_NAME.'_pin');
        $data['dotpay_test'] = $this->config->get(self::PLUGIN_NAME.'_test');
        $data['dotpay_sort_order'] = (isset($this->request->post['dotpay_sort_order']) ? $this->request->post['dotpay_sort_order'] : $this->config->get(self::PLUGIN_NAME.'_sort_order'));
        $data['dotpay_username'] = $this->config->get(self::PLUGIN_NAME.'_username');
        $data['dotpay_password'] = $this->config->get(self::PLUGIN_NAME.'_password');
        
        $data['dotpay_oc'] = $this->config->get(self::PLUGIN_NAME.'_oc');
        $data['dotpay_pv'] = $this->config->get(self::PLUGIN_NAME.'_pv');
        $data['dotpay_pv_id'] = $this->config->get(self::PLUGIN_NAME.'_pv_id');
        $data['dotpay_pv_pin'] = $this->config->get(self::PLUGIN_NAME.'_pv_pin');
        $data['dotpay_pv_curr'] = $this->config->get(self::PLUGIN_NAME.'_pv_curr');
        $data['dotpay_cc'] = $this->config->get(self::PLUGIN_NAME.'_cc');
        $data['dotpay_mp'] = $this->config->get(self::PLUGIN_NAME.'_mp');
        $data['dotpay_blik'] = $this->config->get(self::PLUGIN_NAME.'_blik');
        $data['dotpay_widget'] = $this->config->get(self::PLUGIN_NAME.'_widget');
        
        $data['dotpay_status_completed'] = (isset($this->request->post[self::PLUGIN_NAME.'_status_completed']) ? $this->request->post[self::PLUGIN_NAME.'_status_completed'] : $this->config->get(self::PLUGIN_NAME.'_status_completed'));
        $data['dotpay_status_rejected'] = (isset($this->request->post[self::PLUGIN_NAME.'_status_rejected']) ? $this->request->post[self::PLUGIN_NAME.'_status_rejected'] : $this->config->get(self::PLUGIN_NAME.'_status_rejected'));
        $data['dotpay_status_processing'] = (isset($this->request->post[self::PLUGIN_NAME.'_status_processing']) ? $this->request->post[self::PLUGIN_NAME.'_status_processing'] : $this->config->get(self::PLUGIN_NAME.'_status_processing'));
        $data['dotpay_status_return'] = (isset($this->request->post[self::PLUGIN_NAME.'_status_return']) ? $this->request->post[self::PLUGIN_NAME.'_status_return'] : $this->config->get(self::PLUGIN_NAME.'_status_return'));
        
        $data['dotpay_plugin_version'] = $this->config->get(self::PLUGIN_NAME.'_plugin_version');
        $data['dotpay_api_version'] = $this->config->get(self::PLUGIN_NAME.'_api_version');
        $data['dotpay_URL'] = $this->config->get(self::PLUGIN_NAME.'_URL');
        $data['dotpay_URLC'] = $this->config->get(self::PLUGIN_NAME.'_URLC');
        $data['dotpay_ip'] = $this->config->get(self::PLUGIN_NAME.'_ip');
        $data['dotpay_office_ip'] = $this->config->get(self::PLUGIN_NAME.'_office_ip');
        
        $data['cards'] = $this->model_payment_dotpay_oc->getAllCards();
        
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        $data['return_statuses'] = $this->model_localisation_return_status->getReturnStatuses();  
        $data['error'] = (!empty($this->error) ? $this->error : null);
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        
        $this->response->setOutput($this->load->view($this->getName().'.tpl', $data));
    }
    
    /**
     * Action called for removing saved card
     */
    public function ocremove() {
        if(!isset($this->request->post['card_id']))
            die("OpenCart - LACK OF OID");
        $this->load->model(dirname(dirname($this->getName())).'/dotpay_oc');
        $this->model_payment_dotpay_oc->deleteCardForId($this->request->post['card_id']);
        die('OK');
    }
    
    /**
     * Action called during install this plugin
     */
    public function install() {
        $this->load->model('setting/setting');
        $this->load->model(dirname($this->getName()).'/dotpay_oc');
        $this->load->model(dirname($this->getName()).'/dotpay_info');
        ModelPaymentDotpayOc::install($this->db);
        ModelPaymentDotpayInfo::install($this->db);

        $this->model_setting_setting->editSetting(self::PLUGIN_NAME, $this->settings);
    }

    /**
     * Action called during uninstall this plugin
     */
    public function uninstall() {

        $this->load->model('setting/setting');
        $this->load->model(dirname($this->getName()).'/dotpay_oc');
        $this->load->model(dirname($this->getName()).'/dotpay_info');
        ModelPaymentDotpayOc::uninstall($this->db);
        ModelPaymentDotpayInfo::uninstall($this->db);
        
        $this->model_setting_setting->deleteSetting(self::PLUGIN_NAME);
    }
    
    /**
     * Checks, if settings data is validate, before a saving of changes
     * @return bool
     */
    private function validate() {
        if (!$this->user->hasPermission('modify', $this->getName()))
            $this->error['permission'] = $this->language->get('error_permission');
        if (!$this->request->post[self::PLUGIN_NAME.'_id'])
            $this->error['dotpay_id'] = $this->language->get('error_dotpay_id');
        if (!$this->request->post[self::PLUGIN_NAME.'_pin'])
            $this->error['dotpay_pin'] = $this->language->get('error_dotpay_pin');

        if ($this->request->post[self::PLUGIN_NAME.'_URL']!==self::URL 
            || $this->request->post[self::PLUGIN_NAME.'_URLC']!==self::URLC 
            || $this->request->post[self::PLUGIN_NAME.'_api_version']!==self::API_VERSION
            || $this->request->post[self::PLUGIN_NAME.'_plugin_version']!==self::VERSION
        ) {            
            $this->error['permission'] = $this->language->get('error_dotpay_unauthorized_manipulaed');
        }
        
        return (!$this->error ? true : false);
    }
    
    /**
     * Returns array of breadcrumbs for settings page
     * @return array
     */
    private function getBreadcrumbs() {
        $breadcrumbs = array();

        $breadcrumbs[] = array(
            'href' => HTTPS_SERVER . 'index.php?route=common/dashboard&token=' . $this->session->data['token'],
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        );

        $breadcrumbs[] = array(
            'href' => HTTPS_SERVER . 'index.php?route=extension&token=' . $this->session->data['token'],
            'text' => $this->language->get('text_payment'),
            'separator' => ' :: '
        );

        $breadcrumbs[] = array(
            'href' => HTTPS_SERVER . 'index.php?route='.$this->getName().'&token=' . $this->session->data['token'],
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );
        
        return $breadcrumbs;
    }
}

?>