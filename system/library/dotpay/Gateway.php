<?php

/**
 * NOTICE OF LICENSE.
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
 *  @copyright PayPro S.A.
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

namespace dotpay;

/**
 * Class Provides main functionality of Dotpay payments.
 */
class Gateway
{
    /**
     * Name of plugin.
     */
    const PLUGIN_NAME = 'dotpay_next';
    

    /**
     * Number of One Click channel.
     */
    const OC = 248;

    /**
     * Number of separated card channel for currencies channel.
     */
    const PV = 248;

    /**
     * Number of credit card channel.
     */
    const CC = 248;  //or 246

    /**
     * Number of Blik channel.
     */
    const BLIK = 73;

    /**
     * Number of MasterPass channel.
     */
    const MP = 71;

    /**
     * Name of payment operation.
     */
    const OPERATION_TYPE_PAYMENT = 'payment';

    /**
     * Name of refund operation.
     */
    const OPERATION_TYPE_REFUND = 'refund';

    /**
     * Name of completed status of operations.
     */
    const OPERATION_STATUS_COMPLETED = 'completed';

    /**
     * Configuration object.
     *
     * @var Config
     */
    private $config;

    /**
     * Order details.
     *
     * @var array
     */
    private $order;

    /**
     * Language object.
     *
     * @var Language
     */
    private $language;

    /**
     * Currency object.
     *
     * @var Currency
     */
    private $currency;

    /**
     * Loader object.
     *
     * @var Loader
     */
    private $load;

    /**
     * Session object.
     *
     * @var Session
     */
    private $session;

    /**
     * Request object.
     *
     * @var Request
     */
    private $request;

    /**
     * Customer object.
     *
     * @var Customer
     */
    private $customer;

    /**
     * Registry class with shop environment data.
     *
     * @var Registry
     */
    private $registry;

    /**
     * Prepare object for using.
     *
     * @param Registry $registry Registry object with environment data
     */
    public function __construct($registry)
    {
        $this->registry = $registry;
    }

    /**
     * Returns value of property, using Registry object.
     *
     * @param string $key Name of property
     *
     * @return type
     */
    public function __get($key)
    {
        return $this->registry->get($key);
    }

    /**
     * Returns full path of Dotpay payment extension.
     *
     * @return string
     */
    private function getExtensionName()
    {
        return $this->request->get['route'];
    }

    /**
     * Sets variables, required for correct work.
     *
     * @param Config   $config   Config object
     * @param array    $order    Order details
     * @param Language $language Language object
     * @param Currency $currency Currency object
     * @param Loader   $load     Loader object
     * @param Session  $session  Session object
     * @param Request  $request  Request object
     * @param Customer $customer Customer object
     */
    public function setVars($config, $order, $language, $currency, $load, $session, $request, $customer)
    {
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
    * Get the server variable REMOTE_ADDR, or the first ip of HTTP_X_FORWARDED_FOR (when using proxy)
    * @return string $remote_addr ip of client
    */

    public function getClientIp($list_ip = null)
    {
        $ipaddress = '';
        // CloudFlare support
        if (array_key_exists('HTTP_CF_CONNECTING_IP', $_SERVER)) {
            // Validate IP address (IPv4/IPv6)
            if (filter_var($_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
                $ipaddress = $_SERVER['HTTP_CF_CONNECTING_IP'];
                return $ipaddress;
            }
        }
        if (array_key_exists('X-Forwarded-For', $_SERVER)) {
            $_SERVER['HTTP_X_FORWARDED_FOR'] = $_SERVER['X-Forwarded-For'];
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')) {
                $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $ipaddress = $ips[0];
            } else {
                $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        } else {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        }


        if (isset($list_ip) && $list_ip != null) {
            if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
                return  $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else if (array_key_exists('HTTP_CF_CONNECTING_IP', $_SERVER)) {
                return $_SERVER["HTTP_CF_CONNECTING_IP"];
            } else if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
                return $_SERVER["REMOTE_ADDR"];
            }
        } else {
            return $ipaddress;
        }
    }
 

 /**
     * replacing removing double or more special characters that appear side by side by space from: firstname, lastname, city, street, p_info...
     * @return string
     */
    public function replaceCharacters($originalValue)
		{
			$originalValue1 = preg_replace('/(\s{2,}|\.{2,}|@{2,}|\-{2,}|\/{2,} | \'{2,}|\"{2,}|_{2,})/', ' ', $originalValue);
			return trim($originalValue1);
		}

	/**
	 * checks and crops the size of a string
	 * the $special parameter means an estimate of how many urlencode characters can be used in a given field
	 * e.q. 'ż' (1 char) -> '%C5%BC' (6 chars)
	 */
	public function encoded_substrParams($string, $from, $to, $special=0)
		{
			$s = html_entity_decode($this->replaceCharacters($string),ENT_QUOTES, 'UTF-8');
			$sub = mb_substr($s, $from, $to,'UTF-8');
			$sum = strlen(urlencode($sub));

			if($sum  > $to)
				{
					$newsize = $to - $special;
					$sub = mb_substr($s, $from, $newsize,'UTF-8');
				}
			return trim($sub);
		}


    /**
	 * prepare data for the firstname and lastname so that it would be consistent with the validation
	 */
	public function NewPersonName($value)
    {
        $NewPersonName1 = preg_replace('/[^\p{L}0-9\s\-_]/u',' ',$value);
        return $this->encoded_substrParams($NewPersonName1,0,50,24);
    }    


    /**
	 * prepare data for the city so that it would be consistent with the validation
	 */
	public function NewCity($value)
    {
        $NewCity1 = preg_replace('/[^\p{L}0-9\.\s\-\/_,]/u',' ',$value);
        return $this->encoded_substrParams($NewCity1,0,50,24);
    }


	/**
	 * prepare data for the street so that it would be consistent with the validation
	 */
	public function NewStreet($value)
		{
			$NewStreet1 = preg_replace('/[^\p{L}0-9\.\s\-\/_,]/u',' ',$value);
			return $this->encoded_substrParams($NewStreet1,0,100,50);
		}


    /**
	 * prepare data for the street_n1 so that it would be consistent with the validation
	 */
	public function NewStreet_n1($value)
    {
        $NewStreet_n1a = preg_replace('/[^\p{L}0-9\s\-_\/]/u',' ',$value);
        return $this->encoded_substrParams($NewStreet_n1a,0,30,24);
    }



    /**
	 * prepare data for the phone so that it would be consistent with the validation
	 */
	public function NewPhone($value)
    {
        $NewPhone1 = preg_replace('/[^\+\s0-9\-_]/','',$value);
        return $this->encoded_substrParams($NewPhone1,0,20,6);
    }


    /**
     * prepare data for the postcode so that it would be consistent with the validation
     */
    public function NewPostcode($value)
        {
            $NewPostcode1 = preg_replace('/[^\d\w\s\-]/','',$value);
            return $this->encoded_substrParams($NewPostcode1,0,20,6);
        }


    /**
     * prepare data for the for the title of shop so that it would be consistent with the validation
     * @return string
     */
    public function NewpInfo($value)
		{
			$NewShop_name1 = preg_replace('/[^\p{L}0-9\s\"\/\\:\.\$\+!#\^\?\-_@]/u','',$value);
			return $this->encoded_substrParams($NewShop_name1,0,300,60);
		}



    /**
     * Returns data with fields for all available channels.
     *
     * @return array
     */
    public function getHiddenFields()
    {
        $this->load->language($this->getExtensionName());
        $data = array();
        $id = $this->config->get($this->getConfigKey('id'));
        $pin = $this->config->get($this->getConfigKey('pin'));
        switch ($this->request->post['dotpay-channel-type']) {
            case 'oc':
                $data = $this->getHiddenFieldsOc();
                break;
            case 'pv':
                $data = $this->getHiddenFieldsPv();
                $id = $this->config->get($this->getConfigKey('pv_id'));
                $pin = $this->config->get($this->getConfigKey('pv_pin'));
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
        $data['chk'] = (string)self::generateCHK($pin, $data);

        return $data;
    }

    /**
     * Creates order in shop.
     */
    public function createOrder()
    {
        $this->load->model('checkout/order');
        $this->model_checkout_order->addOrderHistory(
            $this->order['order_id'],
            $this->config->get($this->getConfigKey('status_processing'))
        );
    }

    /**
     * Sets order as paid.
     *
     * @param int $orderId
     */
    public function payForOrder($orderId)
    {
        $this->load->model('checkout/order');
        $this->model_checkout_order->addOrderHistory(
            $orderId,
            $this->config->get($this->getConfigKey('status_completed'))
        );
    }


    /**
     * Returns if the given ip is on the given whitelist.
    *
    * @param string $ip        The ip to check.
    * @param array  $whitelist The ip whitelist. An array of strings.
    *
    * @return bool
    */
    public function isAllowedIp($ip, array $whitelist)
    {
        $ip = (string)$ip;
        if (in_array($ip, $whitelist, true)) {
            return true;
        }

        return false;
    }


    /**
     * Confirms a payment through URLC request, using POST data.
     */
    public function confirm()
    {
        $this->load->language($this->getExtensionName());
        $this->load->model(dirname(dirname($this->getExtensionName())).'/dotpay_oc');
        $this->load->model(dirname(dirname($this->getExtensionName())).'/dotpay_next');
        

        if( ((int) $this->config->get($this->getConfigKey('nonproxy'))) == 1) {
            $CHECK_IP = $_SERVER['REMOTE_ADDR'];
            $proxy_desc = 'FALSE';
        }else{
            $CHECK_IP = $this->getClientIp();
            $proxy_desc = 'TRUE';
        }


        if ( strtoupper($_SERVER['REQUEST_METHOD']) == 'GET') {
            if ( $CHECK_IP == $this->config->get($this->getConfigKey('office_ip')) ) 
            {
                die('Dotpay module ver: '.$this->config->get($this->getConfigKey('plugin_version')).
                    '<br />OpenCart ver: '.VERSION.
                    '<br />ID: '.$this->config->get($this->getConfigKey('id')).
                    '<br />Active: '.(int) $this->config->get($this->getConfigKey('status')).
                    '<br />Test: '.(int) $this->config->get($this->getConfigKey('test')).
                    '<br />Server does not use a proxy: '.(int) $this->config->get($this->getConfigKey('nonproxy')).
                    '<br /> REMOTE ADDRESS: '.$_SERVER['REMOTE_ADDR'].
                    '<br />Api: '.$this->config->get($this->getConfigKey('api_version')).
                    '<br />pvID: '.$this->config->get($this->getConfigKey('pv_id')).
                    '<br />pvCurr: '.$this->config->get($this->getConfigKey('pv_curr')).
                    '<br />PHP: '.PHP_VERSION
                );
            }else {
                die('Access denied! IP: '.$this->getClientIp('checkip').'/'.$_SERVER['REMOTE_ADDR'].', PROXY: '.$proxy_desc.', METHOD: '.$_SERVER['REQUEST_METHOD']);
            }
        }



        if (!( ($this->isAllowedIp($CHECK_IP,$this->config->get($this->getConfigKey('ip')))) && (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') ))
         {
            die('OpenCart - ERROR (REMOTE ADDRESS: '.$this->getClientIp(true).'/'.$_SERVER["REMOTE_ADDR"].', PROXY: '.$proxy_desc.', METHOD: '.$_SERVER['REQUEST_METHOD'].')');
         }


        
        if (!isset($this->request->post['control'])) {
            die('OpenCart - LACK OF OID');
        }


        if (isset($this->request->post['control'])) {

            $control = explode('|', (string)$this->request->post['control']);
                if(is_numeric($control[0]) == false) {
                    die('OpenCart - OID iS NOT VALID');
                }

        }


        if (!$this->checkConfirm()) {
            die('OpenCart - ERROR SIGN');
        }

        $id = ($this->isSelectedPvChannel()) ? $this->config->get($this->getConfigKey('pv_id')) : $this->config->get($this->getConfigKey('id'));
        if ($this->request->post['id'] != $id) {
            die('OpenCart - ERROR ID');
        }

        if ($this->request->post['operation_original_currency'] != $this->order['currency_code']) {
            die('OpenCart - NO MATCH OR WRONG CURRENCY - '.$this->request->post['operation_original_currency'].' <> '.$this->order['currency_code']);
        }

        $receivedAmount = $this->getDataFromRequest('operation_original_amount');
        $originalAmount = self::correctAmount($this->order, $this->currency);
        if ($receivedAmount != $originalAmount) {
            die('OpenCart - NO MATCH OR WRONG AMOUNT - '.$receivedAmount.' <> '.$originalAmount);
        }

        $newOrderState = $this->getNewOrderState();

        if ($this->getDataFromRequest('operation_type') == self::OPERATION_TYPE_PAYMENT) {
            if ($newOrderState === null) {
                die('OpenCart - WRONG TRANSACTION STATUS');
            }

            if ($this->config->get($this->getConfigKey('status_completed')) == $this->order['order_status_id']) {
                die('OpenCart - ORDER IS ALERADY PAID');
            }

            if ($newOrderState == $this->config->get($this->getConfigKey('status_completed'))) {
                $cc = $this->model_extension_payment_dotpay_oc->getCreditCardByOrder($this->order['order_id']);
                if ($cc !== null && $cc['cc_id'] !== null && $cc['card_id'] == null) {
                    $this->load->library('dotpay/SellerApi');
                    $ccInfo = $this->SellerApi->getCreditCardInfo(
                        $this->config->get($this->getConfigKey('username')),
                        $this->config->get($this->getConfigKey('password')),
                        'ID transaction in dotpay: '.$this->getDataFromRequest('operation_number').', payment channel number: "'.$this->getDataFromRequest('channel').'", date this status: '.$this->getDataFromRequest('operation_datetime')
                    );
                    $this->model_extension_payment_dotpay_oc->updateCard($cc['cc_id'], $ccInfo->id, $ccInfo->masked_number, $ccInfo->brand->name);
                }
            }
            $this->load->model('checkout/order');
            $this->model_checkout_order->addOrderHistory($this->order['order_id'], 
                                                         $newOrderState,
                                                         'ID transaction in dotpay: '.$this->getDataFromRequest('operation_number').', payment channel number: "'.$this->getDataFromRequest('channel').'", date this status: '.$this->getDataFromRequest('operation_datetime'), 
                                                         true);
        } elseif ($this->getDataFromRequest('operation_type') == self::OPERATION_TYPE_REFUND) {
            $return = $this->model_extension_payment_dotpay_next->getReturnByOrderId($this->order['order_id']);
            if (!$return) {
                die('Error: '.$this->language->get('error_return'));
            }
            $this->returnOperation($return);
            die('OK');
        } else {
            die('OpenCart - ORDER IS ALERADY PAID');
        }

        die('OK');
    }

    /**
     * Cancels order in shop.
     *
     * @param int $orderId Order id
     */
    public function cancelOrder($orderId)
    {
        $this->load->model('checkout/order');
        $this->model_checkout_order->addOrderHistory(
            $orderId,
            $this->config->get($this->getConfigKey('status_rejected'))
        );
    }

    /**
     * Makes a return operation in shop.
     */
    private function returnOperation($return)
    {
        if (!empty($return) && $return['return_status_id'] != $this->config->get($this->getConfigKey('status_return'))) {
            if ($this->request->post['operation_status'] == self::OPERATION_STATUS_COMPLETED) {
                $data = array(
                    'return_status_id' => $this->config->get($this->getConfigKey('status_return')),
                    'comment' => date('H:i:s ').$this->language->get('text_return_success'),
                    'notify' => 0,
                );

                $this->model_extension_payment_dotpay_next->addReturnHistory($return['return_id'], $data);
            }
        }
    }

	/**
     * Returns correct SERVER NAME or HOSTNAME
     * @return string
     */
    public function getHost()
    {

		$possibleHostSources = array('HTTP_X_FORWARDED_HOST', 'HTTP_HOST', 'SERVER_NAME', 'SERVER_ADDR');
		$sourceTransformations = array(
			"HTTP_X_FORWARDED_HOST" => function($value) {
				$elements = explode(',', $value);
				return trim(end($elements));
			}
		);
		$host = '';
		foreach ($possibleHostSources as $source)
		{
			if (!empty($host)) break;
			if (empty($_SERVER[$source])) continue;
			$host = $_SERVER[$source];
			if (array_key_exists($source, $sourceTransformations))
			{
				$host = $sourceTransformations[$source]($host);
			}
		}

		// Remove port number from host
		$host = preg_replace('/:\d+$/', '', $host);

		return trim($host);

    }

	 /**
	 * The validator checks if the given URL address is correct.
	 */
	public function validateHostname($value)
    {
        return (bool) preg_match('/^[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,10}$/', $value);
    }


     /**
     * Return real HOSTNAME this server
     * @return string
     */   
    public function realHostName() 
    {
        $server_name = '';

        if ($this->validateHostname($this->getHost()))
        {
            $server_name = $this->getHost();
        } else {
            $server_name = "HOSTNAME";
        }

        return $server_name;

    }


    /**
     * Returns basic data of fields of order.
     *
     * @return array
     */
    private function getHiddenFieldsDefault()
    {
        if($this->order['order_id'] !=null ){

                $street = $this->getStreetAndStreetN1();

                if ($this->customer->isLogged()) {
                //if ($this->session->data['account'] == 'register'){
                    if(null !== $this->customer->getTelephone()){
                        $telephone = $this->customer->getTelephone();
                    }else{
                        $telephone = "";
                    }


                }else{
                    if(null !== $this->session->data['guest'] ){
                        $telephone = $this->session->data['guest']['telephone'];
                    }else{
                        $telephone = "";
                    }

                }

                $control_new =  $this->order['order_id'] . '|domain:' . $this->realHostName() . '|OpenCart v'.VERSION.', DP module:' . $this->config->get($this->getConfigKey('plugin_version'));


                $data = array();
                $data['id'] = (string) $this->config->get($this->getConfigKey('id'));
                $data['currency'] = (string) $this->order['currency_code'];
                $data['api_version'] = (string) $this->config->get($this->getConfigKey('api_version'));
                $data['email'] = (string) $this->order['email'];
                $data['lastname'] = (string) $this->NewPersonName($this->order['payment_lastname']);
                $data['firstname'] = (string) $this->NewPersonName($this->order['payment_firstname']);
                $data['control'] = (string) $control_new;
                $data['description'] = (string) $this->language->get('text_order_id').' '.$this->order['order_id'];
                $data['amount'] = self::correctAmount($this->order, $this->currency);

                $data['p_info'] = (string) $this->NewpInfo($this->config->get('config_name'));
                $data['p_email'] = (string) $this->config->get('config_email');
                $data['lang'] = (string) strtolower(substr(trim($this->language->get('code')), 0, 2));
                $data['url'] = (string) HTTPS_SERVER.$this->config->get($this->getConfigKey('URL'));
                $data['urlc'] = (string) HTTPS_SERVER.$this->config->get($this->getConfigKey('URLC'));
                $data['type'] = '4';
                $data['ch_lock'] = '0';
                $data['bylaw'] = '1';
                $data['personal_data'] = '1';
                $data['ignore_last_payment_channel'] = '1';


                if( null != trim($this->NewPhone($telephone)))
                {
                    $data["phone"] = (string) $this->NewPhone($telephone);
                }
                if( null != trim($this->NewCity($this->session->data['payment_address']['city'])) )
                {
                    $data["city"] = (string) $this->NewCity($this->session->data['payment_address']['city']);
                }
                if( null != trim($this->NewStreet($street['street'])) )
                {
                    $data["street"] = (string) $this->NewStreet($street['street']);
                }
                if( null != trim($this->NewStreet_n1($street['street_n1'])) )
                {
                    $data["street_n1"] = (string) $this->NewStreet_n1($street['street_n1']);
                }
                if( null != trim($this->NewPostcode($this->session->data['payment_address']['postcode'])) )
                {
                    $data["postcode"] = (string) $this->NewPostcode($this->session->data['payment_address']['postcode']);
                }
                if( null != trim($this->session->data['payment_address']['country']) )
                {
                    $data["country"] = (string) $this->session->data['payment_address']['country'];
                }


                return $data;
            }else{
                return false;
            }
    }

    /**
     * Returns id of new order state from confirm message.
     *
     * @return type
     */
    public function getNewOrderState()
    {
        $actualState = null;
        switch ($this->getDataFromRequest('operation_status')) {
            case 'new':
                $actualState = $this->config->get($this->getConfigKey('status_processing'));
                break;
            case self::OPERATION_STATUS_COMPLETED:
                $actualState = $this->config->get($this->getConfigKey('status_completed'));
                break;
            case 'rejected':
                $actualState = $this->config->get($this->getConfigKey('status_rejected'));
                break;
            case 'processing':
            case 'processing_realization_waiting':
            case 'processing_realization':
                $actualState = $this->config->get($this->getConfigKey('status_processing'));
        }

        return $actualState;
    }

    /**
     * Returns fields with data of one click payment.
     *
     * @return array
     */
    private function getHiddenFieldsOc()
    {
        $this->load->model(dirname(dirname($this->getExtensionName())).'/dotpay_oc');
        $data = $this->getHiddenFieldsDefault();
        if($data != null) {
                $data['channel'] = (string)self::OC;
                if ($this->request->post['oc_type'] == 'new') {
                    $data['credit_card_store'] = '1';
                    $data['credit_card_customer_id'] = (string)$this->model_extension_payment_dotpay_oc->addCard($this->session->data['customer_id'], $this->order['order_id']);
                } else {
                    $card = $this->model_extension_payment_dotpay_oc->getCardById($this->request->post['card_id']);
                    if ($card == null) {
                        return $data;
                    }
                    $data['credit_card_id'] = (string)$card['card_id'];
                    $data['credit_card_customer_id'] = (string)$card['hash'];
                }

                return $data;
            }else{
                return false;
            }
    }

    /**
     * Returns fields with data of card channel for currencies payment.
     *
     * @return array
     */
    private function getHiddenFieldsPv()
    {
        $data = $this->getHiddenFieldsDefault();
        if($data != null) {
            $data['id'] = (string)$this->config->get($this->getConfigKey('pv_id'));
            $data['channel'] = (string)self::PV;

            return $data;
        }else{
            return false;
        }
    }

    /**
     * Returns fields with data of credit card payment.
     *
     * @return array
     */
    private function getHiddenFieldsCc()
    {
        $data = $this->getHiddenFieldsDefault();
        if($data != null) {
            $data['channel'] = (string)self::CC;

            return $data;
        }else{
            return false;
        }
    }

    /**
     * Returns fields with data of MasterPass payment.
     *
     * @return array
     */
    private function getHiddenFieldsMp()
    {
        $data = $this->getHiddenFieldsDefault();
        if($data != null) {
            if ($this->config->get($this->getConfigKey('test'))) {
                $data['channel'] = '246';
            } else {
                $data['channel'] = (string)self::MP;
            }

            return $data;
        }else{
            return false;
        }
    }

    /**
     * Returns fields with data of Blik payment.
     *
     * @return array
     */
    private function getHiddenFieldsBlik()
    {
        $data = $this->getHiddenFieldsDefault();

        if($data != null) {
            $data['channel'] = (string) self::BLIK;
           // if (!$this->config->get($this->getConfigKey('test'))) {
                $data['blik_code'] = (string) $this->request->post['blik_code'];
           // }

            return $data;
        }else{
            return false;
        }
    }

    /**
     * Returns fields with data of standard Dotpay payment.
     *
     * @return array
     */
    private function getHiddenFieldsDotpay()
    {   
        $data = $this->getHiddenFieldsDefault();

        if($data != null) {
            if ($this->config->get($this->getConfigKey('widget'))) {
                $data['channel'] = (string)$this->request->post['channel'];
            } else {
                $data['type'] = '0';
                $data['ch_lock'] = '0';
            }
    
            return $data;
        }else{
            return false;
        }

    }

    /**
     * Check confirm message from Dotpay.
     *
     * @return bool
     */
    public function checkConfirm()
    {
        if ($this->isSelectedPvChannel()) {
            $start = $this->config->get($this->getConfigKey('pv_pin')).$this->config->get($this->getConfigKey('pv_id'));
        } else {
            $start = $this->config->get($this->getConfigKey('pin')).$this->config->get($this->getConfigKey('id'));
        }
        $signature = $start.
        $this->getDataFromRequest('operation_number').
        $this->getDataFromRequest('operation_type').
        $this->getDataFromRequest('operation_status').
        $this->getDataFromRequest('operation_amount').
        $this->getDataFromRequest('operation_currency').
        $this->getDataFromRequest('operation_withdrawal_amount').
        $this->getDataFromRequest('operation_commission_amount').
        $this->getDataFromRequest('is_completed').
        $this->getDataFromRequest('operation_original_amount').
        $this->getDataFromRequest('operation_original_currency').
        $this->getDataFromRequest('operation_datetime').
        $this->getDataFromRequest('operation_related_number').
        $this->getDataFromRequest('control').
        $this->getDataFromRequest('description').
        $this->getDataFromRequest('email').
        $this->getDataFromRequest('p_info').
        $this->getDataFromRequest('p_email').
		$this->getDataFromRequest('credit_card_issuer_identification_number').
        $this->getDataFromRequest('credit_card_masked_number').
        $this->getDataFromRequest('credit_card_expiration_year').
        $this->getDataFromRequest('credit_card_expiration_month').
        $this->getDataFromRequest('credit_card_brand_codename').
        $this->getDataFromRequest('credit_card_brand_code').
        $this->getDataFromRequest('credit_card_unique_identifier').
        $this->getDataFromRequest('credit_card_id').
		$this->getDataFromRequest('channel').
        $this->getDataFromRequest('channel_country').
        $this->getDataFromRequest('geoip_country');
        $this->getDataFromRequest('payer_bank_account_name').
        $this->getDataFromRequest('payer_bank_account').
        $this->getDataFromRequest('payer_transfer_title').
        $this->getDataFromRequest('blik_voucher_pin').
        $this->getDataFromRequest('blik_voucher_amount').
        $this->getDataFromRequest('blik_voucher_amount_used');

        return $this->request->post['signature'] === hash('sha256', $signature);
    }

    /**
     * Returns flag, if was selected PV channel.
     *
     * @return bool
     */
    public function isSelectedPvChannel()
    {
        return self::isSelectedCurrency($this->config->get($this->getConfigKey('pv_curr')), $this->getDataFromRequest('operation_original_currency'))
           && $this->getDataFromRequest('channel') == self::PV
           && $this->config->get($this->getConfigKey('pv')) == 1
           && $this->config->get($this->getConfigKey('pv_id')) == $this->getDataFromRequest('id');
    }

    /**
     * @param string $name Name of variable from request
     *
     * @return string
     */
    private function getDataFromRequest($name)
    {
        if (isset($this->request->post[$name])) {
            return $this->request->post[$name];
        } else {
            return '';
        }
    }

    /**
     * Returns good street and building name, even if these values are mingled.
     *
     * @return array
     */
    public function getStreetAndStreetN1()
    {
        $street1 = $this->NewStreet($this->session->data['payment_address']['address_1']);
        $street2 = $this->NewStreet_n1($this->session->data['payment_address']['address_2']);
        
        $street = $street1.' '.$street2;

        if(trim($street2) == ''){

            preg_match("/\s[\p{L}0-9\s\-_\/]{1,15}$/u", $street, $matches);
            if (count($matches) > 0) {
                $street_n1 = trim($matches[0]);
                $street = str_replace($matches[0], '', $street);
            } else {
                $street_n1 = '0';
            }

        } else {
            $street = $street1;
            $street_n1 = $street2;
        }

        return array(
            'street' => $street,
            'street_n1' => $street_n1,
        );
    }



    /**
     * Returns amount in correct format.
     *
     * @param array    $order    Order data
     * @param Currency $currency Currency object
     *
     * @return string
     */
    public static function correctAmount($order, $currency)
    {
        return number_format($currency->format($order['total'], $order['currency_code'], $order['currency_value'], false), 2, '.', '');
    }

    /**
     * Checks if payment currency is in the list of allow currencies.
     *
     * @param array  $allowCurrencyForm Array with allowed currency codes
     * @param string $paymentCurrency   Code of payment currency
     *
     * @return bool
     */
    public static function isSelectedCurrency($allowCurrencyForm, $paymentCurrency)
    {
        $result = false;

        $allowCurrency = str_replace(';', ',', $allowCurrencyForm);
        $allowCurrency = strtoupper(str_replace(' ', '', $allowCurrency));
        $allowCurrencyArray = explode(',', trim($allowCurrency));

        if (in_array(strtoupper($paymentCurrency), $allowCurrencyArray)) {
            $result = true;
        }

        return $result;
    }


    /**
     * Generate CHK for seller and payment data
     * @param type $DotpayPin Dotpay seller PIN
     * @param array $ParametersArray parameters of payment
     * @return string
     */
    
    
    ## function: counts the checksum from the defined array of all parameters

    public static function generateCHK($DotpayPin, $ParametersArray)
    {
        if(isset($ParametersArray['chk']))
        {
            unset($ParametersArray['chk']);
        }

            //sorting the parameter list
            ksort($ParametersArray);
            
            // Display the semicolon separated list
            $paramList = implode(';', array_keys($ParametersArray));
            
            //adding the parameter 'paramList' with sorted list of parameters to the array
            $ParametersArray['paramsList'] = $paramList;
            
            //re-sorting the parameter list
            ksort($ParametersArray);
            
            //json encoding  
            $json = json_encode($ParametersArray, JSON_UNESCAPED_SLASHES);

            return hash_hmac('sha256', $json, $DotpayPin, false);hash_hmac('sha256', $json, $DotpayPin, false);
       
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
