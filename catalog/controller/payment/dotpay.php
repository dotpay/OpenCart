<?php

class ControllerPaymentDotpay extends Controller {

    const OPERATION_TYPE_PAYMENT = 'payment';
    const OPERATION_TYPE_REFUND = 'refund';
    
    const OPERATION_STATUS_NEW = 'new';
    const OPERATION_STATUS_PROCESSING = 'processing';
    const OPERATION_STATUS_COMPLETED = 'completed';
    const OPERATION_STATUS_REJECTED = 'rejected';
    const OPERATION_STATUS_REALIZATION_WAIT = 'processing_realization_waiting';
    const OPERATION_STATUS_REALIZATION = 'processing_realization';

    private $error = array();
    
    public function index() {
        
        $this->load->model('checkout/order');
        $this->load->model('setting/setting');      
        $this->load->language('payment/dotpay');
        
        
        $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);  
                
        $data['text_button_confirm'] = $this->language->get('text_button_confirm');        
        
        $data['order_id'] = $order['order_id'];        
        $data['dotpay'] = $this->geParams($order);
       
        $data['action'] = $this->config->get('dotpay_request_url');
        $data['method'] = $this->config->get('dotpay_request_method');
               
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/dotpay.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/payment/dotpay.tpl', $data);
        } else {
            return $this->load->view('default/template/payment/dotpay.tpl', $data);
        }
    }
    
    private function geParams($order){
        
        $data = array();        
       
        $this->load->model('setting/setting');
        $data['id']=$this->config->get('dotpay_id');               
        $data['currency']=$this->config->get('dotpay_currency');                
        $data['p_info'] = $this->config->get('config_name');
        $data['p_email'] = $this->config->get('config_email');               
        $data['api_version'] = $this->config->get('dotpay_api_version');
        $data['lang'] = $this->session->data['language'];           
        $data['email'] = $order['email'];
        $data['lastname'] = $order['payment_lastname'];
        $data['firstname'] = $order['payment_firstname'];
        $data['control'] = $order['order_id'];
        $data['description'] = $order['comment'];            
        $data['amount']=number_format($this->currency->format($order['total'],$data['currency'], $order['currency_value'], FALSE), 2, '.', '');
        
        
//        $data['URL'] = HTTPS_SERVER . $this->config->get('dotpay_URL'); 
//        $data['URLC'] = HTTPS_SERVER . $this->config->get('dotpay_URLC'); 
        $data['URL'] = 'http://56d30c4b.ngrok.com/' . $this->config->get('dotpay_URL'); 
        $data['URLC'] = 'http://56d30c4b.ngrok.com/' . $this->config->get('dotpay_URLC'); 
        $data['type'] = $this->config->get('dotpay_type');
        
        
        return $data;
    }
  
    public function callback(){   
		
        $this->language->load('payment/dotpay');
        $this->document->setTitle($this->language->get('heading_title'));
        
        $data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);
        
        $data['breadcrumbs'][] = array(
            'href' => HTTPS_SERVER . 'index.php?route=payment/dotpay&token=' . $this->session->data['token'],
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );
        
        $data['heading_title'] = $this->language->get('heading_title');      
        $data['text_dotpay_response'] = $this->language->get('heading_title');      
        $data['button_continue'] = $this->language->get('button_continue');      
     
        if (isset($this->request->get['status']) && $this->request->post['status'] == 'OK')
        {
            $data['text_dotpay_info'] = $this->language->get('text_dotpay_success');
            $data['text_dotpay_wait'] = sprintf($this->language->get('text_dotpay_success_wait'), HTTPS_SERVER . 'index.php?route=checkout/success');
            $data['action_continue'] = HTTPS_SERVER . 'index.php?route=checkout/success';           
            
        } else
        {            
            $data['text_dotpay_info'] = $this->language->get('text_dotpay_failure');            
            $data['text_dotpay_wait'] = sprintf($this->language->get('text_dotpay_failure_wait'), HTTPS_SERVER . 'index.php?route=checkout/cart');
            $data['action_continue'] = HTTPS_SERVER . 'index.php?route=checkout/cart';            
        }
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
       
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/dotpay_callback.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/payment/dotpay_callback.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/payment/dotpay_callback.tpl', $data));
		}
    }

    public function confirmation() {
        
//        if (isset($this->request->get['signature']))
//            $this->request->post = $this->request->get;
//        
        foreach ($_POST as $key=>$value){
            error_log("DOTPAY-POST: ".$key . ":" . $value );
        };        
        
        $this->load->model('checkout/order');
        $this->load->language('payment/dotpay');
                
        $orderID = $this->request->post['control'];  
        $order = $this->model_checkout_order->getOrder($orderID);      
        
        if (!$order)
            throw new Exception('Unknown order id.');
                  
        $message = date('H:i:s ') . $this->language->get('text_dotpay_operation_number') . $this->request->post['operation_number'];
        $message .= '. Type: ' . $this->request->post['operation_type'] . '. Status: ' . $this->request->post['operation_status'];
        
        $result = array(
            'message' => $message, 
            'order_status' => $order['order_status_id']
        );
        
        if ($this->request->post['operation_type'] == self::OPERATION_TYPE_PAYMENT)
        {
            if ($this->isValid($this->request->post)){                         
                $this->paymentOperation($result);                   
                echo 'OK';
            } 
            
        } else if ($this->request->post['operation_type'] == self::OPERATION_TYPE_REFUND)
        {
            if ($this->isValid($this->request->post))
            {
                $this->paymentOperation($orderID);
                echo 'OK';
            }
        }    
        
        if (!empty($this->error))
        {           
            $result['message'] .= '. Error: ';
            foreach ($this->error as $key=>$lang){
                $result['message'] .= $this->language->get($lang) . ', ';                
            }  
            $result['order_status'] = $this->config->get('dotpay_status_rejected');
        }   
        
        $this->model_checkout_order->addOrderHistory($orderID, $result['order_status'], $result['message'], TRUE);
       
    }
    
    private function paymentOperation(&$result){        
        
        $message = '';
        if ($result['order_status'] != $this->config->get('dotpay_status_completed'))
        {            
            if ($this->request->post['operation_status'] == self::OPERATION_STATUS_COMPLETED){
                $message = $this->language->get('text_dotpay_success');
                $result['order_status'] = $this->config->get('dotpay_status_completed');
            }else if( $this->request->post['operation_status'] == self::OPERATION_STATUS_REJECTED ) {
                $message = $this->language->get('text_dotpay_failure');
                $result['order_status'] = $this->config->get('dotpay_status_rejected');                                  
            }else {
                $message = $this->language->get('text_dotpay_failure');
                $result['order_status'] = $this->config->get('dotpay_status_processing');
            }
        }
        
        $result['message'] = $result['message'] . '. Info: ' . $message;        
        
    }
    
    private function isValid($params){
                
        if (!$this->calculateSign($params)){
            $this->error['error_signature'] = 'error_signature';
        }       
       
        if ($_SERVER["REMOTE_ADDR"] != $this->config->get('dotpay_ip') ){
            if (!isset($_SERVER["HTTP_X_REAL_IP"]) || (isset($_SERVER["HTTP_X_REAL_IP"]) && $_SERVER["HTTP_X_REAL_IP"]!=$this->config->get('dotpay_ip'))){
                $this->error['error_address_ip'] = 'error_address_ip';
            }                
        }
            
        return (!$this->error ? true : false);
        
    }
    
    private function calculateSign($params){
        
        $PIN = $this->config->get('dotpay_pin');
        $sign = $PIN . 
                $params['id'] .  
                $params['operation_number'] .  
                $params['operation_type'] .  
                $params['operation_status'] .  
                $params['operation_amount'] . 
                $params['operation_currency'] .  
                $params['operation_original_amount'] .  
                $params['operation_original_currency'] .  
                $params['operation_datetime'] .  
                (isset($params['operation_related_number']) ? $params['operation_related_number'] : '' ) .  
                $params['control'] .
                $params['description'] .
                $params['email'] .
                $params['p_info'] .
                $params['p_email'] .
                $params['channel'] .
                (isset($params['channel_country']) ? $params['channel_country'] : '' ) .  
                (isset($params['geoip_country']) ? $params['geoip_country'] : '' );
        
        
        if (hash('sha256', $sign) == $params['signature']){           
            return true;
        }
        
        return false;
    }

}

?>