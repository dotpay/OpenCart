<?php 

/**
 * User controller
 */
class ControllerPaymentDotpayNew extends Controller {
    /**
     * Name of plugin
     */
    const PLUGIN_NAME = 'dotpay_new';
    
    /**
     * Payment type of operation
     */
    const OPERATION_TYPE_PAYMENT = 'payment';
    
    /**
     * Refund type of operation
     */
    const OPERATION_TYPE_REFUND = 'refund';
    
    /**
     * Name of cash group
     */
    const CASH_GROUP = 'cash';
    
    /**
     * Name of transfers group
     */
    const TRANSFER_GROUP = 'transfers';

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
     * Action, which is executed during checkout view
     */
    public function index() {
        $this->load->model('checkout/order');
        $this->load->model('setting/setting');
        $this->load->model(dirname($this->getName()).'/dotpay_oc');
        $this->load->language('payment/'.self::PLUGIN_NAME);
        
        $this->load->library('dotpay/Gateway');
        $this->load->library('dotpay/Agreements');
        
        $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);  
        
        $data['text_button_confirm'] = $this->language->get('text_dotpay_button_confirm');   
        
        $data['action'] = HTTPS_SERVER . 'index.php?route='.$this->getName().'/preparing';
        if(isset($this->session->data['token']))
            $data['action'] .= '&token=' . $this->session->data['token'];
        
        $channelData = array();
        $channelData['dotpay_url'] = $this->config->get(self::PLUGIN_NAME.'_target_payment_url');
        if($this->customer->isLogged())
            $channelData['oc_cards'] = $this->model_payment_dotpay_oc->getAllCardsForCustomer($this->session->data['customer_id']);
        $channelData['text_see_cards'] = $this->language->get('text_see_cards');
        $channelData['register_card_agreement'] = $this->language->get('register_card_agreement');
        $channelData['url_see_cards'] = HTTPS_SERVER . 'index.php?route='.$this->getName().'/ocmanage';
        $channelData['label_select_oc_card'] = $this->language->get('label_select_oc_card');
        $channelData['label_register_oc_card'] = $this->language->get('label_register_oc_card');
        $channelData['blik_code_label'] = $this->language->get('blik_code_label');
        $channelData['blik_validate'] = $this->language->get('blik_code_validate');
        $channelData['label_selected_channel'] = $this->language->get('label_selected_channel');
        $channelData['label_change_channel'] = $this->language->get('label_change_channel');
        $channelData['label_available_channels'] = $this->language->get('label_available_channels');
        $channelData['widget_visible'] = $this->config->get(self::PLUGIN_NAME.'_widget');
        $channelData['widget'] = array(
            'id' => $this->config->get(self::PLUGIN_NAME.'_id'),
            'amount' => dotpay\Gateway::correctAmount($order,$this->currency),
            'currency' => $order['currency_code'],
            'lang' => $this->session->data['language'],
            'disabled_channels' => $this->getDisabledChannels(),
            'host' => $this->config->get(self::PLUGIN_NAME.'_target_payment_url'),
        );
        $data['channels'] = $this->getAvailableChannels($order, $this->Agreements, $channelData);
        
        return $this->templateLoader(self::PLUGIN_NAME, $data);
    }
    
    /**
     * Action, which is executed during preparing request for Dotpay
     */
    public function preparing() {
        $this->load->language('payment/'.self::PLUGIN_NAME);
        $this->load->model(dirname(dirname($this->getName())).'/dotpay_info');
        $this->load->model('checkout/order');
        $this->load->library('dotpay/Gateway');
        $this->load->library('dotpay/Agreements');
        $this->load->library('dotpay/SellerApi');
        $this->load->library('dotpay/RegisterOrder');
        $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $this->Agreements->setInputVars(
            $this->config->get(self::PLUGIN_NAME.'_target_payment_url'),
            $this->config->get(self::PLUGIN_NAME.'_id'),
            dotpay\Gateway::correctAmount($order,$this->currency),
            $order['currency_code'],
            $this->session->data['language']
        );
        $this->Gateway->setVars($this->config, $order, $this->language, $this->currency, $this->load, $this->session, $this->request, $this->customer);
        $hiddenFields = $this->Gateway->getHiddenFields();
        if(isset($this->request->post['channel']) &&
           $this->isFullConfigOk() && 
           $this->Agreements->isChannelInGroup($this->request->post['channel'], array(self::CASH_GROUP, self::TRANSFER_GROUP))
        ) {
            $payment = $this->RegisterOrder->create($hiddenFields);
            if($payment !== NULL) {
                if(isset($payment['instruction']['recipient'])) {
                    $bankAccount = $payment['instruction']['recipient']['bank_account_number'];
                } else {
                    $bankAccount = NULL;
                }
                $this->model_payment_dotpay_info->addInstruction(
                    $this->session->data['order_id'],
                    $payment['operation']['number'],
                    $this->model_payment_dotpay_info->gethashFromPayment($payment),
                    $bankAccount,
                    $this->Agreements->isChannelInGroup($payment['operation']['payment_method']['channel_id'], array(self::CASH_GROUP)),
                    $payment['instruction']['amount'],
                    $payment['instruction']['currency'],
                    $payment['operation']['payment_method']['channel_id']
                );
            } else {
                return $this->generateRequestForm($hiddenFields, $order);
            }
            $orderId = $this->session->data['order_id'];
            $this->load->controller('checkout/success', array());
            $this->response->redirect($this->url->link(dirname($this->getName()).'/info', 'order='.$orderId, 'SSL'));
            die();
        }
        $this->generateRequestForm($hiddenFields, $order);
    }
    
    /**
     * Prepares data and generates form for request from Dotpay
     * @param array $hiddenFields Fields for hidden form
     * @param type $order Array with order data
     */
    private function generateRequestForm($hiddenFields, $order) {
        $this->Gateway->setVars($this->config, $order, $this->language, $this->currency, $this->load, $this->session, $this->request, $this->customer);
        $this->response->setOutput($this->templateLoader(self::PLUGIN_NAME.'_prepare', array(
            'fields' => $hiddenFields,
            'action' => $this->config->get(self::PLUGIN_NAME.'_target_payment_url'),
            'title' => $this->language->get('preparing_title')
        )));
        $this->Gateway->createOrder();
    }
    
    /**
     * Action, which is executed when customer is coming back to shop from Dotpay
     */
    public function back() {
        $this->load->language('payment/'.self::PLUGIN_NAME);
        $this->load->model('checkout/order');
        $this->load->library('dotpay/Gateway');
        if(isset($this->session->data['order_id']))
            $orderId = $this->session->data['order_id'];
        else if(isset($this->session->data['last_order_id']))
            $orderId = $this->session->data['last_order_id'];
        else
            $orderId = NULL;
        $order = $this->model_checkout_order->getOrder($orderId);
        $this->Gateway->setVars($this->config, $order, $this->language, $this->currency, $this->load, $this->session, $this->request, $this->customer);
        $data = array();
        $data['message'] = NULL;
        if(isset($this->request->get['error_code'])) {
            $this->load->controller('checkout/success', array());
            switch($this->request->get['error_code']) {
                case 'PAYMENT_EXPIRED':
                    $data['message'] = $this->language->get('error_payment_expired');
                    break;
                case 'UNKNOWN_CHANNEL':
                    $data['message'] = $this->language->get('error_unknown_channel');
                    break;
                case 'DISABLED_CHANNEL':
                    $data['message'] = $this->language->get('error_disabled_channel');
                    break;
                case 'BLOCKED_ACCOUNT':
                    $data['message'] = $this->language->get('error_blocked_account');
                    break;
                case 'INACTIVE_SELLER':
                    $data['message'] = $this->language->get('error_inactive_seller');
                    break;
                case 'AMOUNT_TOO_LOW':
                    $data['message'] = $this->language->get('error_amount_too_low');
                    break;
                case 'AMOUNT_TOO_HIGH':
                    $data['message'] = $this->language->get('error_amount_too_high');
                    break;
                case 'BAD_DATA_FORMAT':
                    $data['message'] = $this->language->get('error_bad_data_format');
                    break;
                case 'HASH_NOT_EQUAL_CHK':
                    $data['message'] = $this->language->get('error_hash_not_equal');
                    break;
                default:
                    $data['message'] = $this->language->get('error_default');
            }
        }
        if($this->customer->isLogged())
            $data['back_redirect_url'] = HTTPS_SERVER . 'index.php?route=account/order';
        else
            $data['back_redirect_url'] = HTTPS_SERVER . 'index.php?route=common/home';
        $data['check_status_url'] = HTTPS_SERVER . 'index.php?route='.dirname($this->getName()).'/status';
        $data['back_waiting_message'] = $this->language->get('back_waiting_message1').'<br />'.$this->language->get('back_waiting_message2');
        $data['back_success_message'] = $this->language->get('back_success_message');
        $data['back_error_message'] = $this->language->get('back_error_message');
        $data['back_timeout_message'] = $this->language->get('back_timeout_message').'&nbsp;'.$order['order_id'];
        $data['back_text_back'] = $this->language->get('back_text_back');
        $this->document->setTitle($this->language->get('back_title'));
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->session->data['last_order_id'] = $order['order_id'];
        $this->load->controller('checkout/success', array());
        $this->response->setOutput($this->templateLoader(self::PLUGIN_NAME.'_back',$data));
    }
    
    /**
     * Action, which is executed during checking a status of the payment
     */
    public function status() {
        if(isset($this->session->data['last_order_id']) && $this->session->data['last_order_id'] !== NULL) {
            $this->load->model('checkout/order');
            $order = $this->model_checkout_order->getOrder($this->session->data['last_order_id']);
            switch($order['order_status_id']) {
                case $this->config->get(self::PLUGIN_NAME.'_status_processing'):
                    die('0');
                case $this->config->get(self::PLUGIN_NAME.'_status_completed'):
                    unset($this->session->data['last_order_id']);
                    die('1');
                default:
                    unset($this->session->data['last_order_id']);
                    die('-1');
            }
        } else {
            die('NO');
        }
    }
    
    /**
     * Action executed when Dotpay send requests with URLC status confirmation
     */
    public function confirm() {
        $this->load->model('checkout/order');
        $this->load->library('dotpay/Gateway');
        if(!isset($this->request->post['control']))
            $control = NULL;
        else
            $control = $this->request->post['control'];
        $order = $this->model_checkout_order->getOrder($control);
        $this->Gateway->setVars($this->config, $order, $this->language, $this->currency, $this->load, $this->session, $this->request, $this->customer);
        $this->Gateway->confirm();
    }
    
    /**
     * Action which allows to manage saved credit cards by a user
     */
    public function ocmanage() {
        $this->load->language('payment/'.self::PLUGIN_NAME);
        $this->load->model(dirname(dirname($this->getName())).'/dotpay_oc');
        $this->document->setTitle($this->language->get('ocmanage_title'));
        if(!isset($this->session->data['customer_id']))
            die('You have to login into this page!');
        $cards = $this->model_payment_dotpay_oc->getAllCardsForCustomer($this->session->data['customer_id']);
        $data = array();
        $data['ocmanage_alert_info'] = $this->language->get('ocmanage_alert_info');
        $data['ocmanage_card_number'] = $this->language->get('ocmanage_card_number');
        $data['ocmanage_card_brand'] = $this->language->get('ocmanage_card_brand');
        $data['ocmanage_register_date'] = $this->language->get('ocmanage_register_date');
        $data['ocmanage_deregister'] = $this->language->get('ocmanage_deregister');
        $data['ocmanage_deregister_card'] = $this->language->get('ocmanage_deregister_card');
        $data['ocmanage_on_remove_message'] = $this->language->get('ocmanage_on_remove_message');
        $data['ocmanage_on_done_message'] = $this->language->get('ocmanage_on_done_message');
        $data['ocmanage_on_failure_message'] = $this->language->get('ocmanage_on_failure_message');
        $data['ocmanage_remove_url'] = HTTPS_SERVER . 'index.php?route='.dirname($this->getName()).'/ocremove';
        $data['cards'] = $cards;
        $data['cards_exists'] = (bool)count($cards);
        $data['ocmanage_alert_notfound'] = $this->language->get('ocmanage_alert_notfound');
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->templateLoader(self::PLUGIN_NAME.'_ocmanage',$data));
    }
    
    /**
     * Action executed during a request, which should remove selected credit card
     */
    public function ocremove() {
        if(!isset($this->request->post['card_id']))
            die("OpenCart - LACK OF OID");
        $this->load->model(dirname(dirname($this->getName())).'/dotpay_oc');
        $this->model_payment_dotpay_oc->deleteCardForId($this->request->post['card_id']);
        die('OK');
    }
    
    /**
     * Action executed during displaying a instruction with end of payment, during paying a cash or a transfer
     */
    public function info() {
        try {
            $route = preg_replace('/[^a-zA-Z0-9_\/]/', '', 'dotpay/simple_html_dom');	
            $file = DIR_SYSTEM . 'library/' . $route . '.php';
            if (is_file($file))
                include_once($file);
        } catch(Exception $e) {}
        $this->load->language('payment/'.self::PLUGIN_NAME);
        $this->load->library('dotpay/Agreements');
        $this->load->library('dotpay/Gateway');
        $this->load->model(dirname(dirname($this->getName())).'/dotpay_info');
        $this->load->model('checkout/order');
        $this->document->setTitle($this->language->get('info_title'));
        $data = array();
        if(!isset($this->session->data['customer_id']))
            $data['info_order_not_found'] = $this->language->get('login_required');
        else if(isset($this->request->get['order']) && !empty($this->request->get['order'])) {
            $order = $this->model_checkout_order->getOrder($this->request->get['order']);
            $this->Agreements->setInputVars(
                $this->config->get(self::PLUGIN_NAME.'_target_payment_url'),
                $this->config->get(self::PLUGIN_NAME.'_id'),
                dotpay\Gateway::correctAmount($order,$this->currency),
                $order['currency_code'],
                $this->session->data['language']
            );
            $instruction = $this->model_payment_dotpay_info->getByOrderId($this->request->get['order']);
            if($instruction != NULL) {
                $chData = $this->Agreements->getChannelData($instruction['channel']);
                if($instruction['is_cash'])
                    $data['info_info'] = $this->language->get('info_info_cash');
                else
                    $data['info_info'] = $this->language->get('info_info_bank');
                $data['info_order_not_found'] = NULL;
                $data['info_account'] = $this->language->get('info_account');
                $data['info_amount'] = $this->language->get('info_amount');
                $data['info_title'] = $this->language->get('info_title');
                $data['info_name'] = $this->language->get('info_name');
                $data['info_street'] = $this->language->get('info_street');
                $data['info_postcode'] = $this->language->get('info_postcode');
                $data['info_logo'] = $this->language->get('info_logo');
                $data['info_warning'] = $this->language->get('info_warning');
                $data['bank_account'] = $instruction['bank_account'];
                $data['amount'] = $instruction['amount'];
                $data['currency'] = $instruction['currency'];
                $data['title'] = $instruction['number'];
                $data['name'] = ModelPaymentDotpayInfo::DOTPAY_NAME;
                $data['street'] = ModelPaymentDotpayInfo::DOTPAY_STREET;
                $data['postcode'] = ModelPaymentDotpayInfo::DOTPAY_CITY;
                if($instruction['is_cash']) {
                    $data['address'] = $this->model_payment_dotpay_info->getPdfUrl(
                        $this->config->get(self::PLUGIN_NAME.'_target_payment_url'),
                        $instruction['number'],
                        $instruction['hash']
                    );
                    $data['command'] = $this->language->get('info_command_cash');
                } else {
                    $data['address'] = $this->model_payment_dotpay_info->getBankPage(
                        $this->config->get(self::PLUGIN_NAME.'_target_payment_url'),
                        $instruction['number'],
                        $instruction['hash']
                    );
                    $data['command'] = $this->language->get('info_command_transfer');
                }
                $data['logo'] = $chData['logo'];
            } else {
                $data['info_order_not_found'] = $this->language->get('info_info_not_found');
            }
        } else {
            $data['info_order_not_found'] = $this->language->get('info_order_not_found');
        }
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->templateLoader(self::PLUGIN_NAME.'_info',$data));
    }
    
    /**
     * Renders channel template
     * @param array $order Order details
     * @param string $name Name of channel
     * @param dotpay\Agreements $agreements Object of Dotpay Agreements
     * @param array $data Payment data for Dotpay
     * @return string
     */
    private function renderChannel($order, $name, dotpay\Agreements $agreements, $data, $number = NULL) {
        $id = ($name==='pv')?$this->config->get(self::PLUGIN_NAME.'_pv_id'):$this->config->get(self::PLUGIN_NAME.'_id');
        $agreements->setInputVars(
            $this->config->get(self::PLUGIN_NAME.'_target_payment_url'),
            $id,
            dotpay\Gateway::correctAmount($order,$this->currency),
            $order['currency_code'],
            $this->session->data['language']
        );
        if($number!== NULL && !$agreements->getChannelData($number))
            return '';
        $data['name'] = $name;
        $data['title'] = $this->language->get($name.'_channel_title');
        $data['bylaw'] = $agreements->getByLaw();
        $data['personal_data'] = $agreements->getPersonalData();
        $data['content'] = $this->templateLoader('dotpay_channels/'.$name, $data);
        if($name == 'dotpay' && !$data['widget_visible'])
            $data['no_agreements'] = true;
        return $this->templateLoader('dotpay_channels/template', $data);
    }
    
    /**
     * Returns HTML with all available channels
     * @param array $order Order details
     * @param dotpay\Agreements $agreements Object of Dotpay Agreements
     * @param array $data Payment data for Dotpay
     * @return string
     */
    private function getAvailableChannels($order, dotpay\Agreements $agreements, $data) {
        $this->load->library('dotpay/Gateway');
        $channels = array(
            'oc' => dotpay\Gateway::OC,
            'pv' => dotpay\Gateway::PV,
            'cc' => dotpay\Gateway::CC,
            'mp' => dotpay\Gateway::MP,
            'blik' => dotpay\Gateway::BLIK
        );
        if(dotpay\Gateway::isSelectedCurrency($this->config->get(self::PLUGIN_NAME.'_pv_curr'), $order['currency_code']))
            unset($channels['cc']);
        else unset($channels['pv']);
        if(!$this->customer->isLogged())
            unset($channels['oc']);
        $html = '';
        foreach($channels as $channel => $number) {
            if($this->config->get(self::PLUGIN_NAME.'_'.$channel))
                $html .= $this->renderChannel($order, $channel, $agreements, $data, $number);
        }
        return $html.$this->renderChannel($order, 'dotpay', $agreements, $data);
    }
    
    /**
     * Returns list with IDs of disabled channels, separated by comma
     * @return string
     */
    private function getDisabledChannels() {
        $disabled = array();
        if($this->config->get(self::PLUGIN_NAME.'_oc') ==1 && $this->customer->isLogged())
            $disabled[] = dotpay\Gateway::OC;
        if($this->config->get(self::PLUGIN_NAME.'_pv') == 1 || $this->config->get(self::PLUGIN_NAME.'_cc') == 1)
            $disabled[] = dotpay\Gateway::CC;
        if($this->config->get(self::PLUGIN_NAME.'_mp') ==1 )
            $disabled[] = dotpay\Gateway::MP;
        if($this->config->get(self::PLUGIN_NAME.'_blik') ==1 )
            $disabled[] = dotpay\Gateway::BLIK;
        return implode(',',$disabled);
    }
    
    /**
     * Returns a flag, if full confuguration of seller is correct
     * @return bool
     */
    private function isFullConfigOk() {
        $id = $this->config->get(self::PLUGIN_NAME.'_id');
        $pin = $this->config->get(self::PLUGIN_NAME.'_pin');
        $username = $this->config->get(self::PLUGIN_NAME.'_username');
        $password = $this->config->get(self::PLUGIN_NAME.'_password');
        return (
            !empty($id) ||
            !empty($pin) ||
            !empty($username) ||
            !empty($password)
        );
    }
    
    /**
     * Generates HTML code from template based od given data
     * @param string $name Template name
     * @param array $data Data for template
     * @return string
     */
    private function templateLoader($name, $data) {
        if (version_compare(VERSION, '2.2.0.0', '<')) {
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/'.$name.'.tpl')) {
                return $this->load->view($this->config->get('config_template') . '/template/payment/'.$name.'.tpl', $data);
            } else {
                return $this->load->view('default/template/payment/'.$name.'.tpl', $data);
            }
        } else {
            return $this->load->view('payment/'.$name, $data);
        }
    }
}

?>