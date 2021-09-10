<?php

/**
 * User controller.
 */
class ControllerExtensionPaymentDotpayNext extends Controller
{
    /**
     * Name of plugin.
     */
    const PLUGIN_NAME = 'dotpay_next';

    /**
     * Payment type of operation.
     */
    const OPERATION_TYPE_PAYMENT = 'payment';

    /**
     * Refund type of operation.
     */
    const OPERATION_TYPE_REFUND = 'refund';

    /**
     * Name of cash group.
     */
    const CASH_GROUP = 'cash';



    /**
     * Name of transfers group.
     */
    const TRANSFER_GROUP = 'transfers';

    /**
     * Is enable payment on site for method payment cash and transfer groups
     */
    const RO_ENABLED = true;


    /**
     * Returns full path of Dotpay payment extension.
     *
     * @return string
     */
    private function getExtensionName()
    {
        return 'extension/payment/'.self::PLUGIN_NAME;
    }

    /**
     * Action, which is executed during checkout view.
     */
    public function index()
    {
        $this->load->model('checkout/order');
        $this->load->model('setting/setting');
        $this->load->model(dirname($this->getExtensionName()).'/dotpay_oc');
        $this->load->language($this->getExtensionName());

        $this->load->library('dotpay/Gateway');
        $this->load->library('dotpay/Agreements');
        $this->load->library('dotpay/TemplateLoader');

        $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $data['text_button_confirm'] = $this->language->get('text_dotpay_button_confirm');

        $data['action'] = $this->createUrl('preparing');
        if (isset($this->session->data['user_token'])) {
            $data['action'] .= '&user_token='.$this->session->data['user_token'];
        }

        $channelData = array();
        $channelData['dotpay_url'] = $this->config->get($this->getConfigKey('target_payment_url'));
        if ($this->customer->isLogged()) {
            $channelData['oc_cards'] = $this->model_extension_payment_dotpay_oc->getAllCardsForCustomer($this->session->data['customer_id']);
        }
        $channelData['HTTPS_SERVER'] = HTTPS_SERVER;
        $channelData['text_see_cards'] = $this->language->get('text_see_cards');
        $channelData['register_card_agreement'] = $this->language->get('register_card_agreement');
        $channelData['url_see_cards'] = $this->createUrl('ocmanage');
        $channelData['label_select_oc_card'] = $this->language->get('label_select_oc_card');
        $channelData['label_register_oc_card'] = $this->language->get('label_register_oc_card');
        $channelData['blik_code_label'] = $this->language->get('blik_code_label');
        $channelData['blik_validate'] = $this->language->get('blik_code_validate');
        $channelData['label_selected_channel'] = $this->language->get('label_selected_channel');
        $channelData['label_change_channel'] = $this->language->get('label_change_channel');
        $channelData['label_available_channels'] = $this->language->get('label_available_channels');
        $channelData['widget_visible'] = $this->config->get($this->getConfigKey('widget'));
        $channelData['widget'] = array(
            'id' => $this->config->get($this->getConfigKey('id')),
            'amount' => dotpay\Gateway::correctAmount($order, $this->currency),
            'currency' => $order['currency_code'],
            'lang' => strtolower(substr(trim($this->session->data['language']), 0, 2)),
            'disabled_channels' => $this->getDisabledChannels(),
            'host' => $this->config->get($this->getConfigKey('target_payment_url')),
        );
        $data['channels'] = $this->getAvailableChannels($order, $this->Agreements, $channelData);

        return $this->templateLoader(self::PLUGIN_NAME, $data);
    }

    /**
     * Action, which is executed during preparing request for Dotpay.
     */
    public function preparing()
    {
        $this->load->language($this->getExtensionName());
        $this->load->model(dirname($this->getExtensionName()).'/dotpay_info');
        $this->load->model('checkout/order');
        $this->load->library('dotpay/Gateway');
        $this->load->library('dotpay/Agreements');
        $this->load->library('dotpay/SellerApi');
        $this->load->library('dotpay/RegisterOrder');
        $this->load->library('dotpay/TemplateLoader');
        if (isset($this->session->data['order_id'])) {
             $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);
             $get_currency = $this->currency;
             $correctAmount = dotpay\Gateway::correctAmount($order, $get_currency);
        }else{
            $order = null; 
            $get_currency = null;
            $correctAmount = null;
            //return null;
        }
        $this->Agreements->setInputVars(
            $this->config->get($this->getConfigKey('target_payment_url')),
            $this->config->get($this->getConfigKey('id')),
            $correctAmount,
            $order['currency_code'],
            $this->session->data['language']
        );
        $this->Gateway->setVars($this->config, $order, $this->language, $get_currency, $this->load, $this->session, $this->request, $this->customer);
        $hiddenFields = $this->Gateway->getHiddenFields();
        if (isset($this->request->post['channel']) &&
            self::RO_ENABLED == true &&
            $this->isFullConfigOk() &&
            $order != null &&
           $this->Agreements->isChannelInGroup($this->request->post['channel'], array(self::CASH_GROUP, self::TRANSFER_GROUP))
        ) {
            $payment = $this->RegisterOrder->create($hiddenFields);
            if ($payment !== null) {
                if (isset($payment['instruction']['recipient'])) {
                    $bankAccount = $payment['instruction']['recipient']['bank_account_number'];
                } else {
                    $bankAccount = null;
                }
                $this->model_extension_payment_dotpay_info->addInstruction(
                    $this->session->data['order_id'],
                    $payment['operation']['number'],
                    $this->model_extension_payment_dotpay_info->gethashFromPayment($payment),
                    $bankAccount,
                    $this->Agreements->isChannelInGroup($payment['operation']['payment_method']['channel_id'], array(self::CASH_GROUP)),
                    $payment['instruction']['amount'],
                    $payment['instruction']['currency'],
                    $payment['operation']['payment_method']['channel_id']
                );
            } else {
                return $this->generateRequestForm($hiddenFields, $order);
            }
           // $orderId = $this->session->data['order_id'];
            if (isset($this->session->data['order_id'])) {
                $orderId = $this->session->data['order_id'];
            } elseif (isset($this->session->data['last_order_id'])) {
                $orderId = $this->session->data['last_order_id'];
            } else {
                $orderId = null;
            }

            $this->load->controller('checkout/success', array());
            //$this->response->redirect($this->url->link(dirname($this->getExtensionName()).'/info', 'order='.$orderId, 'SSL'));
            $this->response->redirect($this->url->link($this->getExtensionName().'/info', 'order='.$orderId, 'SSL'));
            die();
        }
        $this->generateRequestForm($hiddenFields, $order);
    }

    /**
     * Prepares data and generates form for request from Dotpay.
     *
     * @param array $hiddenFields Fields for hidden form
     * @param type  $order        Array with order data
     */
    private function generateRequestForm($hiddenFields, $order)
    {
        $this->Gateway->setVars($this->config, $order, $this->language, $this->currency, $this->load, $this->session, $this->request, $this->customer);
        if ($this->customer->isLogged()) {
            $noOrderback = HTTPS_SERVER.'index.php?route=account/order';
        } else {
            $noOrderback = HTTPS_SERVER.'index.php?route=common/home';
        }
        $this->response->setOutput($this->templateLoader(self::PLUGIN_NAME.'_prepare', array(
            'fields' => $hiddenFields,
            'orderNR' => $order,
            'text_info1' => $this->language->get('preparing_noorder_txt1'),
            'text_info2' => $this->language->get('preparing_noorder_txt2'),
            'text_info3' => $this->language->get('preparing_noorder_txt3'),
            'noOrder_back_link' => $noOrderback,
            'noOrder_back_txt' => $this->language->get('preparing_noorder_txt4'),
            'action' => $this->config->get($this->getConfigKey('target_payment_url')),
            'title' => $this->language->get('preparing_title'),
        )));
        $this->Gateway->createOrder();
    }

    /**
     * Action, which is executed when customer is coming back to shop from Dotpay.
     */
    public function back()
    {
        $this->load->language($this->getExtensionName());
        $this->load->model('checkout/order');
        $this->load->library('dotpay/Gateway');
        $this->load->library('dotpay/TemplateLoader');
        if (isset($this->session->data['order_id'])) {
            $orderId = $this->session->data['order_id'];
        } elseif (isset($this->session->data['last_order_id'])) {
            $orderId = $this->session->data['last_order_id'];
        } else {
            $orderId = null;
        }

        $order = $this->model_checkout_order->getOrder($orderId);
        $this->Gateway->setVars($this->config, $order, $this->language, $this->currency, $this->load, $this->session, $this->request, $this->customer);
        $data = array();
        $data['message'] = null;
        if (isset($this->request->get['error_code'])) {
            $this->load->controller('checkout/success', array());
            switch ($this->request->get['error_code']) {
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
        if ($this->customer->isLogged()) {
            $data['back_redirect_url'] = HTTPS_SERVER.'index.php?route=account/order';
        } else {
            $data['back_redirect_url'] = HTTPS_SERVER.'index.php?route=common/home';
        }

        $data['check_status_url'] = $this->createUrl('status');
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
        $this->response->setOutput($this->templateLoader(self::PLUGIN_NAME.'_back', $data));
    }

    /**
     * Action, which is executed during checking a status of the payment.
     */
    public function status()
    {
        if (isset($this->session->data['last_order_id']) && $this->session->data['last_order_id'] !== null) {
            $this->load->model('checkout/order');
            $order = $this->model_checkout_order->getOrder($this->session->data['last_order_id']);
            switch ($order['order_status_id']) {
                case $this->config->get($this->getConfigKey('status_processing')):
                    die('0');
                case $this->config->get($this->getConfigKey('status_completed')):
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
     * Action executed when Dotpay send requests with URLC status confirmation.
     */
    public function confirm()
    {
        $this->load->model('checkout/order');
        $this->load->library('dotpay/Gateway');
        if(!isset($this->request->post['control']))
        {
            $control_org = NULL;
        } else{
            $control_org = $this->request->post['control'];
        }

        $reg_control = '/id:#(\d+)\|domain:/m';
        preg_match_all($reg_control, (string)$control_org, $matches_control, PREG_SET_ORDER, 0);

        if(count($matches_control) == 1 && (isset($matches_control[0][1]) && (int)$matches_control[0][1] >0)){
    
            $controlNr =  (int)$matches_control[0][1];
        }else {

            $controlNr1 = explode('|', (string)$control_org);
            $controlNr2 = explode('id:#', (string)$controlNr1[0]);
            if(count($controlNr2) >1) {
                $controlNr = $controlNr2[1];
            }else{
                $controlNr = $controlNr2[0];
            }
            
        }
        $order = $this->model_checkout_order->getOrder($controlNr);
        $this->Gateway->setVars($this->config, $order, $this->language, $this->currency, $this->load, $this->session, $this->request, $this->customer);
        $this->Gateway->confirm();

    }

    /**
     * Action which allows to manage saved credit cards by a user.
     */
    public function ocmanage()
    {
        $this->load->language($this->getExtensionName());
        $this->load->model(dirname($this->getExtensionName()).'/dotpay_oc');
        $this->load->library('dotpay/TemplateLoader');
        $this->document->setTitle($this->language->get('ocmanage_title'));
        if (!isset($this->session->data['customer_id'])) {
            die('You have to login into this page!');
        }
        $cards = $this->model_extension_payment_dotpay_oc->getAllCardsForCustomer($this->session->data['customer_id']);
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
        $data['ocmanage_remove_url'] = $this->createUrl('ocremove');
        $data['cards'] = $cards;
        $data['cards_exists'] = (bool) count($cards);
        $data['ocmanage_alert_notfound'] = $this->language->get('ocmanage_alert_notfound');
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->templateLoader(self::PLUGIN_NAME.'_ocmanage', $data));
    }

    /**
     * Action executed during a request, which should remove selected credit card.
     */
    public function ocremove()
    {
        if (!isset($this->request->post['card_id'])) {
            die('OpenCart - LACK OF OID');
        }
        $this->load->model(dirname($this->getExtensionName()).'/dotpay_oc');
        $this->model_extension_payment_dotpay_oc->deleteCardForId($this->request->post['card_id']);
        die('OK');
    }

    /**
     * Action executed during displaying a instruction with end of payment, during paying a cash or a transfer.
     */
    public function info()
    {
        try {
            $route = preg_replace('/[^a-zA-Z0-9_\/]/', '', 'dotpay/simple_html_dom');
            $file = DIR_SYSTEM.'library/'.$route.'.php';
            if (is_file($file)) {
                include_once $file;
            }
        } catch (Exception $e) {
        }
        $this->load->language($this->getExtensionName());
        $this->load->library('dotpay/Agreements');
        $this->load->library('dotpay/Gateway');
        $this->load->library('dotpay/TemplateLoader');
        $this->load->model(dirname($this->getExtensionName()).'/dotpay_info');
        $this->load->model('checkout/order');
        $this->document->setTitle($this->language->get('info_title'));
        $data = array();
            /*
                if (!isset($this->session->data['customer_id'])) {
                    $data['dp_info_order_not_found'] = $this->language->get('login_required');
                } elseif (isset($this->request->get['order']) && !empty($this->request->get['order'])) {
            */    
         if (isset($this->request->get['order']) && !empty($this->request->get['order'])) {  

            $order = $this->model_checkout_order->getOrder($this->request->get['order']);
            $this->Agreements->setInputVars(
                $this->config->get($this->getConfigKey('target_payment_url')),
                $this->config->get($this->getConfigKey('id')),
                dotpay\Gateway::correctAmount($order, $this->currency),
                $order['currency_code'],
                $this->session->data['language']
            );
            $instruction = $this->model_extension_payment_dotpay_info->getByOrderId($this->request->get['order']);
            if ($instruction != NULL) {

                $chData = $this->Agreements->getChannelData($instruction['channel']);
                if ($instruction['is_cash']) {
                    $data['info_info'] = $this->language->get('info_info_cash');
                } else {
                    $data['info_info'] = $this->language->get('info_info_bank');
                }
                $data['dp_info_order_not_found'] = NULL;
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
               // $data['title'] = $instruction['number'];
                $data['title'] = $instruction['number']." ".$this->language->get('text_order_id')." ".$this->request->get['order'];
                $data['name'] = ModelExtensionPaymentDotpayInfo::DOTPAY_NAME;
                $data['street'] = ModelExtensionPaymentDotpayInfo::DOTPAY_STREET;
                $data['postcode'] = ModelExtensionPaymentDotpayInfo::DOTPAY_CITY;
                if ($instruction['is_cash']) {
                    $data['address'] = $this->model_extension_payment_dotpay_info->getPdfUrl(
                        $this->config->get($this->getConfigKey('target_payment_url')),
                        $instruction['number'],
                        $instruction['hash']
                    );
                    $data['command'] = $this->language->get('info_command_cash');
                } else {
                    $data['address'] = $this->model_extension_payment_dotpay_info->getBankPage(
                        $this->config->get($this->getConfigKey('target_payment_url')),
                        $instruction['number'],
                        $instruction['hash']
                    );
                    $data['command'] = $this->language->get('info_command_transfer');
                }
                $data['logo'] = $chData['logo'];
            } else {
                $data['dp_info_order_not_found'] = $this->language->get('info_info_not_found');
            }
        } else {
            $data['dp_info_order_not_found'] = $this->language->get('dp_info_order_not_found');
        }
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->templateLoader(self::PLUGIN_NAME.'_info', $data));
    }

    /**
     * Renders channel template.
     *
     * @param array             $order      Order details
     * @param string            $name       Name of channel
     * @param dotpay\Agreements $agreements Object of Dotpay Agreements
     * @param array             $data       Payment data for Dotpay
     *
     * @return string
     */
    private function renderChannel($order, $name, dotpay\Agreements $agreements, $data, $number = null)
    {
        $id = ($name === 'pv') ? $this->config->get($this->getConfigKey('pv_id')) : $this->config->get($this->getConfigKey('id'));
        $agreements->setInputVars(
            $this->config->get($this->getConfigKey('target_payment_url')),
            $id,
            dotpay\Gateway::correctAmount($order, $this->currency),
            $order['currency_code'],
            $this->session->data['language']
        );
        if ($number !== null && !$agreements->getChannelData($number)) {
            return '';
        }
        $data['name'] = $name;
        $data['title'] = $this->language->get($name.'_channel_title');
        $data['bylaw'] = $agreements->getByLaw();
        $data['personal_data'] = $agreements->getPersonalData();
        $data['content'] = $this->templateLoader('dotpay_channels/'.$name, $data);
        if ($name == 'dotpay' && !$data['widget_visible']) {
            $data['no_agreements'] = true;
        }

        return $this->templateLoader('dotpay_channels/template', $data);
    }

    /**
     * Returns HTML with all available channels.
     *
     * @param array             $order      Order details
     * @param dotpay\Agreements $agreements Object of Dotpay Agreements
     * @param array             $data       Payment data for Dotpay
     *
     * @return string
     */
    private function getAvailableChannels($order, dotpay\Agreements $agreements, $data)
    {
        $this->load->library('dotpay/Gateway');
        $channels = array(
            'oc' => dotpay\Gateway::OC,
            'pv' => dotpay\Gateway::PV,
            'cc' => dotpay\Gateway::CC,
            'mp' => dotpay\Gateway::MP,
            'blik' => dotpay\Gateway::BLIK,
        );
        if (dotpay\Gateway::isSelectedCurrency($this->config->get($this->getConfigKey('pv_curr')), $order['currency_code'])) {
            unset($channels['cc']);
        } else {
            unset($channels['pv']);
        }
        if (!$this->customer->isLogged()) {
            unset($channels['oc']);
        }
        $html = '';
        foreach ($channels as $channel => $number) {
            if ($this->config->get($this->getConfigKey($channel))) {
                $html .= $this->renderChannel($order, $channel, $agreements, $data, $number);
            }
        }

        return $html.$this->renderChannel($order, 'dotpay', $agreements, $data);
    }

    /**
     * Returns list with IDs of disabled channels, separated by comma.
     *
     * @return string
     */
    private function getDisabledChannels()
    {
        $disabled = array();
        if ($this->config->get($this->getConfigKey('oc')) == 1 && $this->customer->isLogged()) {
            $disabled[] = dotpay\Gateway::OC;
        }
        if ($this->config->get($this->getConfigKey('pv')) == 1 || $this->config->get($this->getConfigKey('cc')) == 1) {
            $disabled[] = dotpay\Gateway::CC;
        }
        if ($this->config->get($this->getConfigKey('mp')) == 1) {
            $disabled[] = dotpay\Gateway::MP;
        }
        if ($this->config->get($this->getConfigKey('blik')) == 1) {
            $disabled[] = dotpay\Gateway::BLIK;
        }

        return implode(',', $disabled);
    }

    /**
     * Returns a flag, if full confuguration of seller is correct.
     *
     * @return bool
     */
    private function isFullConfigOk()
    {
        $id = $this->config->get($this->getConfigKey('id'));
        $pin = $this->config->get($this->getConfigKey('pin'));
        $username = $this->config->get($this->getConfigKey('username'));
        $password = $this->config->get($this->getConfigKey('password'));

        return
            !empty($id) ||
            !empty($pin) ||
            !empty($username) ||
            !empty($password)
        ;
    }

    /**
     * Generates HTML code from template based od given data.
     *
     * @param string $name Template name
     * @param array  $data Data for template
     *
     * @return string
     */
    private function templateLoader($name, $data)
    {
        if (version_compare(VERSION, '2.2.0.0', '<')) {
            if (file_exists(DIR_TEMPLATE.$this->config->get('config_template').'/template/payment/'.$name)) {
                return $this->TemplateLoader->view($this->config->get('config_template').'/template/payment/'.$name, $data);
            } else {
                return $this->TemplateLoader->view('default/template/payment/'.$name, $data);
            }
        } else {
            return $this->TemplateLoader->view('payment/'.$name, $data);
        }
    }
    
    /**
     * This view loader allows to force disable template caching
     *
     * @param	string	$route
     * @param	array	$data
     *
     * @return	string
     */
    public function view($route, $data = array()) {
           // Sanitize the call
           $route = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$route);

           // Keep the original trigger
           $trigger = $route;

           // Template contents. Not the output!
           $template = '';

           // Trigger the pre events
           $result = $this->registry->get('event')->trigger('view/' . $trigger . '/before', array(&$route, &$data, &$template));

           // Make sure its only the last event that returns an output if required.
           if ($result && !$result instanceof Exception) {
                   $output = $result;
           } else {
                   $template = new Template($this->registry->get('config')->get('template_engine'));

                   foreach ($data as $key => $value) {
                           $template->set($key, $value);
                   }

                   $output = $template->render($this->registry->get('config')->get('template_directory') . $route, $this->registry->get('config')->get('template_cache'));		
           }

           // Trigger the post events
           $result = $this->registry->get('event')->trigger('view/' . $trigger . '/after', array(&$route, &$data, &$output));

           if ($result && !$result instanceof Exception) {
                   $output = $result;
           }

           return $output;
    }

    /**
     * Create an url with given value as 'route' parameter
     * @param string $route Specific route for url
     * @return string
     */
    private function createUrl($route)
    {
        return HTTPS_SERVER.'index.php?route='.$this->getExtensionName().'/'.$route;
    }
    
    /**
     * Get full name of value used in configuration
     * @param string $key Name of value
     * @return mixed
     */
    private function getConfigKey($key)
    {
        return 'payment_'.self::PLUGIN_NAME.'_'.$key;
    }
}
