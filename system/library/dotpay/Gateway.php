<?php

/**
*
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to tech@dotpay.pl so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade OpenCart to newer
* versions in the future. If you wish to customize OpenCart for your
* needs please refer to http://www.dotpay.pl for more information.
*
*  @author    Dotpay Team <tech@dotpay.pl>
*  @copyright Dotpay
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
*/

namespace dotpay;

/**
 * Class Provides main functionality of Dotpay payments
 */
class Gateway {
    /**
     * Name of plugin
     */
    const PLUGIN_NAME = 'dotpay_new';
    
    /**
     * Number of One Click channel
     */
    const OC = 248;
    
    /**
     * Number of separated card channel for currencies channel
     */
    const PV = 248;
    
    /**
     * Number of credit card channel
     */
    const CC = 246;
    
    /**
     * Number of Blik channel
     */
    const BLIK = 73;
    
    /**
     * Number of MasterPass channel
     */
    const MP = 71;
    
    /**
     * Name of payment operation
     */
    const OPERATION_TYPE_PAYMENT = 'payment';
    
    /**
     * Name of refund operation
     */
    const OPERATION_TYPE_REFUND = 'refund';
    
    /**
     * Name of completed status of operations
     */
    const OPERATION_STATUS_COMPLETED = 'completed';
    
    /**
     * Configuration object
     * @var Config 
     */
    private $config;
    
    /**
     * Order details
     * @var array 
     */
    private $order;
    
    /**
     * Language object
     * @var Language 
     */
    private $language;
    
    /**
     * Currency object
     * @var Currency 
     */
    private $currency;
    
    /**
     * Loader object
     * @var Loader 
     */
    private $load;
    
    /**
     * Session object
     * @var Session 
     */
    private $session;
    
    /**
     * Request object
     * @var Request 
     */
    private $request;
    
    /**
     * Customer object
     * @var Customer 
     */
    private $customer;
    
    /**
     * Registry class with shop environment data
     * @var Registry 
     */
    private $registry;
    
    /**
     * Prepare object for using
     * @param Registry $registry Registry object with environment data
     */
    public function __construct($registry) {
        $this->registry = $registry;
    }
    
    /**
     * Returns value of property, using Registry object
     * @param string $key Name of property
     * @return type
     */
    public function __get($key) {
        return $this->registry->get($key);
    }
    
    /**
     * Returns full path of Dotpay payment extension
     * @return string
     */
    private function getExtensionName() {
        return $this->request->get['route'];
    }
    
    /**
     * Sets variables, required for correct work
     * @param Config $config Config object
     * @param array $order Order details
     * @param Language $language Language object
     * @param Currency $currency Currency object
     * @param Loader $load Loader object
     * @param Session $session Session object
     * @param Request $request Request object
     * @param Customer $customer Customer object
     */
    public function setVars($config, $order, $language, $currency, $load, $session, $request, $customer) {
        $this->config = $config;
        $this->order = $order;
        $this->language = $language;
        $this->currency = $currency;
        $this->load = $load;
        $this->session = $session;
        $this->request = $request;
        $this->customer = $customer;
    }
    
    /**
     * Returns data with fields for all available channels
     * @return array
     */
    public function getHiddenFields() {
        $this->load->language($this->getExtensionName());
        $data = array();
        $id = $this->config->get(self::PLUGIN_NAME.'_id');
        $pin = $this->config->get(self::PLUGIN_NAME.'_pin');
        switch($this->request->post['dotpay-channel-type']) {
            case 'oc':
                $data = $this->getHiddenFieldsOc();
                break;
            case 'pv':
                $data = $this->getHiddenFieldsPv();
                $id = $this->config->get(self::PLUGIN_NAME.'_pv_id');
                $pin = $this->config->get(self::PLUGIN_NAME.'_pv_pin');
                break;
            case 'cc':
                $data = $this->getHiddenFieldsCc();
                break;
            case 'mp':
                $data = $this->getHiddenFieldsMp();
                break;
            case 'blik':
                $data = $this->getHiddenFieldsBlik();
                break;
            default:
                $data = $this->getHiddenFieldsDotpay();
        }
        $data['chk'] = self::generateCHK($id, $pin, $data);
        return $data;
    }
    
    /**
     * Creates order in shop
     */
    public function createOrder() {
        $this->load->model('checkout/order');
        $this->model_checkout_order->addOrderHistory(
            $this->order['order_id'],
            $this->config->get(self::PLUGIN_NAME.'_status_processing')
        );
    }
    
    /**
     * Sets order as paid
     * @param int $orderId
     */
    public function payForOrder($orderId) {
        $this->load->model('checkout/order');
        $this->model_checkout_order->addOrderHistory(
            $orderId,
            $this->config->get(self::PLUGIN_NAME.'_status_completed')
        );
    }
    
    /**
     * Confirms a payment through URLC request, using POST data
     */
    public function confirm() {
        $this->load->language($this->getExtensionName());
        $this->load->model(dirname(dirname($this->getExtensionName())).'/dotpay_oc');
        $this->load->model(dirname(dirname($this->getExtensionName())).'/dotpay_new');
        if($_SERVER['REMOTE_ADDR'] == $this->config->get(self::PLUGIN_NAME.'_office_ip') && $_SERVER['REQUEST_METHOD'] == 'GET')
            die("OpenCart - M.Ver: ".$this->config->get(self::PLUGIN_NAME.'_plugin_version').
                ", OC.Ver: ". VERSION .
                ", ID: ".$this->config->get(self::PLUGIN_NAME.'_id').
                ", Active: ".(int)$this->config->get(self::PLUGIN_NAME.'_status').
                ", Test: ".(int)$this->config->get(self::PLUGIN_NAME.'_test').
                ", Api: ".$this->config->get(self::PLUGIN_NAME.'_api_version').
                ", pvID: ".$this->config->get(self::PLUGIN_NAME.'_pv_id').
                ", pvCurr: ".$this->config->get(self::PLUGIN_NAME.'_pv_curr').
                ", PHP: ".PHP_VERSION
            );
        if(!isset($this->request->post['control']))
            die("OpenCart - LACK OF OID");
        
        if($_SERVER['REMOTE_ADDR'] != $this->config->get(self::PLUGIN_NAME.'_ip'))
            die("OpenCart - ERROR REMOTE ADDRESS: ".$_SERVER['REMOTE_ADDR'].' <> '.$this->config->get(self::PLUGIN_NAME.'_ip'));

        if ($_SERVER['REQUEST_METHOD'] != 'POST')
            die("OpenCart - ERROR (METHOD <> POST)");
        
        if (!$this->checkConfirm())
            die("OpenCart - ERROR SIGN");
        
        $id = ($this->isSelectedPvChannel())?$this->config->get(self::PLUGIN_NAME.'_pv_id'):$this->config->get(self::PLUGIN_NAME.'_id');
        if ($this->request->post['id'] != $id)
            die("OpenCart - ERROR ID");
        
        if($this->request->post['operation_original_currency'] != $this->order['currency_code'])
            die('OpenCart - NO MATCH OR WRONG CURRENCY - '.$this->request->post['operation_original_currency'].' <> '.$order['currency_code']);
        
        $receivedAmount = $this->getDataFromRequest('operation_original_amount');
        $originalAmount = self::correctAmount($this->order, $this->currency);
        if($receivedAmount != $originalAmount)
            die('OpenCart - NO MATCH OR WRONG AMOUNT - '.$receivedAmount.' <> '.$originalAmount);
        
        $newOrderState = $this->getNewOrderState();
        
        if($this->getDataFromRequest('operation_type') == self::OPERATION_TYPE_PAYMENT) {
            if($newOrderState===NULL)
                die ('OpenCart - WRONG TRANSACTION STATUS');
            
            if($this->config->get(self::PLUGIN_NAME.'_status_completed') == $this->order['order_status_id'])
                die ('OpenCart - ORDER IS ALERADY PAID');
            
            if($newOrderState == $this->config->get(self::PLUGIN_NAME.'_status_completed')) {
                $cc = $this->model_extension_payment_dotpay_oc->getCreditCardByOrder($this->order['order_id']);
                if($cc !== NULL && $cc['cc_id'] !== NULL && $cc['card_id'] == NULL) {
                    $this->load->library('dotpay/SellerApi');
                    $ccInfo = $this->SellerApi->getCreditCardInfo(
                        $this->config->get(self::PLUGIN_NAME.'_username'),
                        $this->config->get(self::PLUGIN_NAME.'_password'),
                        $this->getDataFromRequest('operation_number')
                    );
                    $this->model_extension_payment_dotpay_oc->updateCard($cc['cc_id'], $ccInfo->id, $ccInfo->masked_number, $ccInfo->brand->name); 
                }
            }
            $this->load->model('checkout/order');
            $this->model_checkout_order->addOrderHistory($this->order['order_id'], $newOrderState, $this->getDataFromRequest('operation_number'), TRUE);
        } else if($this->getDataFromRequest('operation_type') == self::OPERATION_TYPE_REFUND) {
            $return = $this->model_extension_payment_dotpay_new->getReturnByOrderId($this->order['order_id']);
            if (!$return) {
                die('Error: ' . $this->language->get('error_return'));
            }
            $this->returnOperation($return);
            die('OK');
        } else {
            die ('OpenCart - ORDER IS ALERADY PAID');
        }
        
        die('OK');
    }
    
    /**
     * Cancels order in shop
     * @param int $orderId Order id
     */
    public function cancelOrder($orderId) {
        $this->load->model('checkout/order');
        $this->model_checkout_order->addOrderHistory(
            $orderId,
            $this->config->get(self::PLUGIN_NAME.'_status_rejected')
        );
    }
    
    /**
     * Makes a return operation in shop
     */
    private function returnOperation($return) {
        if (!empty($return) && $return['return_status_id'] != $this->config->get(self::PLUGIN_NAME.'_status_return') ) {     
            if ($this->request->post['operation_status'] == self::OPERATION_STATUS_COMPLETED) {          
                $data = array(
                    'return_status_id' => $this->config->get(self::PLUGIN_NAME.'_status_return'),
                    'comment' => date('H:i:s ') . $this->language->get('text_return_success'),
                    'notify' => 0
                );
                
                $this->model_extension_payment_dotpay_new->addReturnHistory($return['return_id'], $data);
            }            
        }
    }
    
    /**
     * Returns basic data of fields of order
     * @return array
     */
    private function getHiddenFieldsDefault() {
        $street = $this->getStreetAndStreetN1();
        $data = array();        
        $data['id'] = $this->config->get(self::PLUGIN_NAME.'_id');               
        $data['currency'] = $this->order['currency_code'];
        $data['p_info'] = $this->config->get('config_name');
        $data['p_email'] = $this->config->get('config_email');
        $data['api_version'] = $this->config->get(self::PLUGIN_NAME.'_api_version');
        $data['lang'] = $this->language->get('code');
        $data['email'] = $this->order['email'];
        $data['lastname'] = $this->order['payment_lastname'];
        $data['firstname'] = $this->order['payment_firstname'];
        $data['street'] = $street['street'];
        $data['street_n1'] = $street['street_n1'];
        $data['city'] = $this->session->data['payment_address']['city'];
        $data['postcode'] = $this->session->data['payment_address']['postcode'];
        $data['country'] = $this->session->data['payment_address']['country'];
        $data['phone'] = $this->customer->getTelephone();
        $data['control'] = $this->order['order_id'];
        $data['description'] = $this->language->get('text_order_id').' '.$this->order['order_id'];
        $data['amount'] = self::correctAmount($this->order, $this->currency);
        $data['URL'] = HTTPS_SERVER . $this->config->get(self::PLUGIN_NAME.'_URL'); 
        $data['URLC'] = HTTPS_SERVER . $this->config->get(self::PLUGIN_NAME.'_URLC'); 
        $data['type'] = '4';
        $data['ch_lock'] = '1';
        $data['bylaw'] = '1';
        $data['personal_data'] = '1';
        return $data;
    }
    
    /**
     * Returns id of new order state from confirm message
     * @return type
     */
    public function getNewOrderState() {
        $actualState = NULL;
        switch ($this->getDataFromRequest('operation_status')) {
            case "new":
                $actualState = $this->config->get(self::PLUGIN_NAME.'_status_processing');
                break;
            case self::OPERATION_STATUS_COMPLETED:
                $actualState = $this->config->get(self::PLUGIN_NAME.'_status_completed');
                break;
            case "rejected":
                $actualState = $this->config->get(self::PLUGIN_NAME.'_status_rejected');
                break;
            case "processing":
            case "processing_realization_waiting":
            case "processing_realization":
                $actualState = $this->config->get(self::PLUGIN_NAME.'_status_processing');
        }
        return $actualState;
    }
    
    /**
     * Returns fields with data of one click payment
     * @return array
     */
    private function getHiddenFieldsOc() {
        $this->load->model(dirname(dirname($this->getExtensionName())).'/dotpay_oc');
        $data = $this->getHiddenFieldsDefault();
        $data['channel'] = self::OC;
        if($this->request->post['oc_type'] == 'new') {
            $data['credit_card_store'] = 1;
            $data['credit_card_customer_id'] = $this->model_extension_payment_dotpay_oc->addCard($this->session->data['customer_id'], $this->order['order_id']);
        } else {
            $card = $this->model_extension_payment_dotpay_oc->getCardById($this->request->post['card_id']);
            if($card == NULL)
                return $data;
            $data['credit_card_id'] = $card['card_id'];
            $data['credit_card_customer_id'] = $card['hash'];
        }
        return $data;
    }
    
    /**
     * Returns fields with data of card channel for currencies payment
     * @return array
     */
    private function getHiddenFieldsPv() {
        $data = $this->getHiddenFieldsDefault();
        $data['id'] = $this->config->get(self::PLUGIN_NAME.'_pv_id');
        $data['channel'] = self::PV;
        return $data;
    }
    
    /**
     * Returns fields with data of credit card payment
     * @return array
     */
    private function getHiddenFieldsCc() {
        $data = $this->getHiddenFieldsDefault();
        $data['channel'] = self::CC;
        return $data;
    }
    
    /**
     * Returns fields with data of MasterPass payment
     * @return array
     */
    private function getHiddenFieldsMp() {
        $data = $this->getHiddenFieldsDefault();
        if($this->config->get(self::PLUGIN_NAME.'_test'))
            $data['channel'] = 246;
        else
            $data['channel'] = self::MP;
        return $data;
    }
    
    /**
     * Returns fields with data of Blik payment
     * @return array
     */
    private function getHiddenFieldsBlik() {
        $data = $this->getHiddenFieldsDefault();
        $data['channel'] = self::BLIK;
        if(!$this->config->get(self::PLUGIN_NAME.'_test'))
            $data['blik_code'] = $this->request->post['blik_code'];
        return $data;
    }
    
    /**
     * Returns fields with data of standard Dotpay payment
     * @return array
     */
    private function getHiddenFieldsDotpay() {
        $data = $this->getHiddenFieldsDefault();
        if($this->config->get(self::PLUGIN_NAME.'_widget'))
            $data['channel'] = $this->request->post['channel'];
        else {
            $data['type'] = 0;
            $data['ch_lock'] = 0;
        }
            
        return $data;
    }
    
    /**
     * Check confirm message from Dotpay
     * @return bool
     */
    public function checkConfirm(){
        if($this->isSelectedPvChannel())
            $start = $this->config->get(self::PLUGIN_NAME.'_pv_pin').$this->config->get(self::PLUGIN_NAME.'_pv_id');
        else
            $start = $this->config->get(self::PLUGIN_NAME.'_pin').$this->config->get(self::PLUGIN_NAME.'_id');
        $signature = $start.
        $this->getDataFromRequest('operation_number').
        $this->getDataFromRequest('operation_type').
        $this->getDataFromRequest('operation_status').
        $this->getDataFromRequest('operation_amount').
        $this->getDataFromRequest('operation_currency').
        $this->getDataFromRequest('operation_withdrawal_amount').
        $this->getDataFromRequest('operation_commission_amount').
        $this->getDataFromRequest('operation_original_amount').
        $this->getDataFromRequest('operation_original_currency').
        $this->getDataFromRequest('operation_datetime').
        $this->getDataFromRequest('operation_related_number').
        $this->getDataFromRequest('control').
        $this->getDataFromRequest('description').
        $this->getDataFromRequest('email').
        $this->getDataFromRequest('p_info').
        $this->getDataFromRequest('p_email').
        $this->getDataFromRequest('channel').
        $this->getDataFromRequest('channel_country').
        $this->getDataFromRequest('geoip_country');

        return ($this->request->post['signature'] === hash('sha256', $signature));
    }
    
    /**
     * Returns flag, if was selected PV channel
     * @return bool
     */
    public function isSelectedPvChannel() {
        return (self::isSelectedCurrency($this->config->get(self::PLUGIN_NAME.'_pv_curr'), $this->getDataFromRequest('operation_original_currency')) 
           && $this->getDataFromRequest('channel')==self::PV
           && $this->config->get(self::PLUGIN_NAME.'_pv') == 1
           && $this->config->get(self::PLUGIN_NAME.'_pv_id')==$this->getDataFromRequest('id'));
    }
    
    /**
     * 
     * @param string $name Name of variable from request
     * @return string
     */
    private function getDataFromRequest($name) {
        if(isset($this->request->post[$name]))
            return $this->request->post[$name];
        else
            return '';
    }

    /**
     * Returns good street and building name, even if these values are mingled
     * @return array
     */
    public function getStreetAndStreetN1() {
        $street = $this->session->data['payment_address']['address_1'];
        preg_match("/\s[\w\d\/_\-]{0,30}$/", $street, $matches);
        if(count($matches)>0)
        {
            $street_n1 = trim($matches[0]);
            $street = str_replace($matches[0], '', $street);
        } else {
            $street_n1 = '';
        }
        
        return array(
            'street' => $street,
            'street_n1' => $street_n1
        );
    }
    
    /**
     * Returns amount in correct format
     * @param array $order Order data
     * @param Currency $currency Currency object
     * @return string
     */
    public static function correctAmount($order, $currency) {
        return number_format($currency->format($order['total'], $order['currency_code'], $order['currency_value'], FALSE), 2, '.', '');
    }
    
    /**
     * Checks if payment currency is in the list of allow currencies
     * @param array $allowCurrencyForm Array with allowed currency codes
     * @param string $paymentCurrency Code of payment currency
     * @return boolean
     */
    public static function isSelectedCurrency($allowCurrencyForm, $paymentCurrency) {
        $result = false;

        $allowCurrency = str_replace(';', ',', $allowCurrencyForm);
        $allowCurrency = strtoupper(str_replace(' ', '', $allowCurrency));
        $allowCurrencyArray =  explode(",",trim($allowCurrency));
        
        if(in_array(strtoupper($paymentCurrency), $allowCurrencyArray)) {
            $result = true;
        }
        
        return $result;
    }
    
    /**
     * Returns CHK for request params
     * @param string $DotpayId Dotpay shop ID
     * @param string $DotpayPin Dotpay PIN
     * @param array $ParametersArray Parameters from request
     * @return string
     */
    public static function generateCHK($DotpayId, $DotpayPin, $ParametersArray) {
        $ParametersArray['id'] = $DotpayId;
        $ChkParametersChain =
        $DotpayPin.
        (isset($ParametersArray['api_version']) ?
        $ParametersArray['api_version'] : null).
        (isset($ParametersArray['charset']) ?
        $ParametersArray['charset'] : null).
        (isset($ParametersArray['lang']) ?
        $ParametersArray['lang'] : null).
        (isset($ParametersArray['id']) ?
        $ParametersArray['id'] : null).
        (isset($ParametersArray['amount']) ?
        $ParametersArray['amount'] : null).
        (isset($ParametersArray['currency']) ?
        $ParametersArray['currency'] : null).
        (isset($ParametersArray['description']) ?
        $ParametersArray['description'] : null).
        (isset($ParametersArray['control']) ?
        $ParametersArray['control'] : null).
        (isset($ParametersArray['channel']) ?
        $ParametersArray['channel'] : null).
        (isset($ParametersArray['credit_card_brand']) ?
        $ParametersArray['credit_card_brand'] : null).
        (isset($ParametersArray['ch_lock']) ?
        $ParametersArray['ch_lock'] : null).
        (isset($ParametersArray['channel_groups']) ?
        $ParametersArray['channel_groups'] : null).
        (isset($ParametersArray['onlinetransfer']) ?
        $ParametersArray['onlinetransfer'] : null).
        (isset($ParametersArray['URL']) ?
        $ParametersArray['URL'] : null).
        (isset($ParametersArray['type']) ?
        $ParametersArray['type'] : null).
        (isset($ParametersArray['buttontext']) ?
        $ParametersArray['buttontext'] : null).
        (isset($ParametersArray['URLC']) ?
        $ParametersArray['URLC'] : null).
        (isset($ParametersArray['firstname']) ?
        $ParametersArray['firstname'] : null).
        (isset($ParametersArray['lastname']) ?
        $ParametersArray['lastname'] : null).
        (isset($ParametersArray['email']) ?
        $ParametersArray['email'] : null).
        (isset($ParametersArray['street']) ?
        $ParametersArray['street'] : null).
        (isset($ParametersArray['street_n1']) ?
        $ParametersArray['street_n1'] : null).
        (isset($ParametersArray['street_n2']) ?
        $ParametersArray['street_n2'] : null).
        (isset($ParametersArray['state']) ?
        $ParametersArray['state'] : null).
        (isset($ParametersArray['addr3']) ?
        $ParametersArray['addr3'] : null).
        (isset($ParametersArray['city']) ?
        $ParametersArray['city'] : null).
        (isset($ParametersArray['postcode']) ?
        $ParametersArray['postcode'] : null).
        (isset($ParametersArray['phone']) ?
        $ParametersArray['phone'] : null).
        (isset($ParametersArray['country']) ?
        $ParametersArray['country'] : null).
        (isset($ParametersArray['code']) ?
        $ParametersArray['code'] : null).
        (isset($ParametersArray['p_info']) ?
        $ParametersArray['p_info'] : null).
        (isset($ParametersArray['p_email']) ?
        $ParametersArray['p_email'] : null).
        (isset($ParametersArray['n_email']) ?
        $ParametersArray['n_email'] : null).
        (isset($ParametersArray['expiration_date']) ?
        $ParametersArray['expiration_date'] : null).
        (isset($ParametersArray['recipient_account_number']) ?
        $ParametersArray['recipient_account_number'] : null).
        (isset($ParametersArray['recipient_company']) ?
        $ParametersArray['recipient_company'] : null).
        (isset($ParametersArray['recipient_first_name']) ?
        $ParametersArray['recipient_first_name'] : null).
        (isset($ParametersArray['recipient_last_name']) ?
        $ParametersArray['recipient_last_name'] : null).
        (isset($ParametersArray['recipient_address_street']) ?
        $ParametersArray['recipient_address_street'] : null).
        (isset($ParametersArray['recipient_address_building']) ?
        $ParametersArray['recipient_address_building'] : null).
        (isset($ParametersArray['recipient_address_apartment']) ?
        $ParametersArray['recipient_address_apartment'] : null).
        (isset($ParametersArray['recipient_address_postcode']) ?
        $ParametersArray['recipient_address_postcode'] : null).
        (isset($ParametersArray['recipient_address_city']) ?
        $ParametersArray['recipient_address_city'] : null).
        (isset($ParametersArray['warranty']) ?
        $ParametersArray['warranty'] : null).
        (isset($ParametersArray['bylaw']) ?
        $ParametersArray['bylaw'] : null).
        (isset($ParametersArray['personal_data']) ?
        $ParametersArray['personal_data'] : null).
        (isset($ParametersArray['credit_card_number']) ?
        $ParametersArray['credit_card_number'] : null).
        (isset($ParametersArray['credit_card_expiration_date_year']) ?
        $ParametersArray['credit_card_expiration_date_year'] : null).
        (isset($ParametersArray['credit_card_expiration_date_month']) ?
        $ParametersArray['credit_card_expiration_date_month'] : null).
        (isset($ParametersArray['credit_card_security_code']) ?
        $ParametersArray['credit_card_security_code'] : null).
        (isset($ParametersArray['credit_card_store']) ?
        $ParametersArray['credit_card_store'] : null).
        (isset($ParametersArray['credit_card_store_security_code']) ?
        $ParametersArray['credit_card_store_security_code'] : null).
        (isset($ParametersArray['credit_card_customer_id']) ?
        $ParametersArray['credit_card_customer_id'] : null).
        (isset($ParametersArray['credit_card_id']) ?
        $ParametersArray['credit_card_id'] : null).
        (isset($ParametersArray['blik_code']) ?
        $ParametersArray['blik_code'] : null).
        (isset($ParametersArray['credit_card_registration']) ?
        $ParametersArray['credit_card_registration'] : null).
        (isset($ParametersArray['recurring_frequency']) ?
        $ParametersArray['recurring_frequency'] : null).
        (isset($ParametersArray['recurring_interval']) ?
        $ParametersArray['recurring_interval'] : null).
        (isset($ParametersArray['recurring_start']) ?
        $ParametersArray['recurring_start'] : null).
        (isset($ParametersArray['recurring_count']) ?
        $ParametersArray['recurring_count'] : null);
        return hash('sha256',$ChkParametersChain);
    }
}

?>