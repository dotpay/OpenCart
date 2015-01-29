<?php

class ControllerPaymentDotpay extends Controller {

    private $error = array();
    
    public function index() {
        
        $this->load->model('checkout/order');
        $this->load->library('encryption');
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
        //requried
        $data['id']=$this->config->get('dotpay_id');        
        $data['amount']=number_format($this->currency->format($order['total'],$data['currency'], $order['currency_value'], FALSE), 2, '.', '');
        $data['currency']=$this->config->get('dotpay_currency');
        $data['lang'] = $this->session->data['language'];
        $data['description'] = $order['comment'];               
        $data['p_info'] = $this->config->get('config_name');
        $data['p_email'] = $this->config->get('config_email');       
        $data['control'] = base64_encode($order['order_id']);
        $data['api_version'] = $this->config->get('dotpay_api_version');     
        
        //optional
        $data['URL'] = HTTPS_SERVER . $this->config->get('dotpay_URL'); 
        $data['URLC'] = HTTPS_SERVER . $this->config->get('dotpay_URLC'); 
        $data['type'] = $this->config->get('dotpay_type');
        
        
        return $data;
    }
  
    public function callback(){
        error_log("DOTPAY-POST: " );
        echo 'Potwierdzenie:' . $this->request->get['status'];        
       
    }

    public function confirmation() {
        error_log("DOTPAY-POST: " );
         foreach ($_POST as $key=>$value){
            error_log("DOTPAY-POST: ".$key . ":" . $value );
        }
        
        $this->load->model('checkout/order');
        $this->load->language('payment/dotpay');
        
        
        $order = $this->model_checkout_order->getOrder($order_id);
        $order_id = base64_decode($this->request->post['control']);
        $order = $this->model_checkout_order->getOrder($order_id);
        
        if ($this->isValid($this->request->post)){
            
        }
        
        echo 'OK';
        return false;
       
        

        if (isset($_POST) && !empty($_POST) && isset($_POST['tr_crc']) && isset($_POST['tr_status'])) {
            $seller_id = $_POST['id'];
            $order_id = base64_decode($_POST['tr_crc']);
            $tr_status = $_POST['tr_status'];
            $tr_id = $_POST['tr_id'];
            $amount_paid = number_format($_POST['tr_paid'], 2, '.', '');
            $conf_code = $this->config->get('transferuj_conf_code');
            $md5sum = md5($seller_id . $tr_id . $_POST['tr_amount'] . $_POST['tr_crc'] . $conf_code);

            $tr_error = $_POST['tr_error'];

            $this->load->model('checkout/order');
            $this->load->language('payment/transferuj');

            $order_data = $this->model_checkout_order->getOrder($order_id);
            $completed_status = $this->config->get('transferuj_order_status_completed');
            $error_status = $this->config->get('transferuj_order_status_error');
         
            $current_status = $order_data['order_status_id'];

            if ($md5sum != $_POST['md5sum']) {
                $note = $this->language->get('text_incorrect_md5sum');
                $this->model_checkout_order->addOrderHistory($order_data['order_id'], $this->config->get('transferuj_order_status_error'), $note, TRUE);
                return false;
            }

            if ($current_status != $completed_status) {
                $note = date('H:i:s ') . $this->language->get('text_payment_tr_id') . $tr_id;
                if ($tr_error != 'none')
                    $note .= '<br />' . $this->language->get('text_payment_' . $tr_error) . $amount_paid;

                if ($tr_status == 'TRUE') {
                 
                    $this->model_checkout_order->addOrderHistory($order_data['order_id'], $this->config->get('transferuj_order_status_completed'), $note, TRUE);
                   
                } elseif ($tr_status == 'FALSE') {
                    $this->model_checkout_order->addOrderHistory($order_data['order_id'], $this->config->get('transferuj_order_status_error'), $note, TRUE);
                    
                }
            }
        }
    }
    
    private function isValid($params){
                
        if (!$this->calculateSign($params)){
            $this->error['error_signature'] = 1;
        }
       
        if ($_SERVER["REMOTE_ADDR"] != $this->config->get('dotpay_ip')){
            $this->error['error_address_ip'] = 1;
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
                $params['operation_original-currency'] .  
                $params['operation_datetime'] .  
                $params['operation_related_number'] .  
                $params['control'] .
                $params['description'] .
                $params['email'] .
                $params['p_info'] .
                $params['p_email'] .
                $params['channel'];
               
        if (hash('sha256', $sign) == $params['signature']){
            return true;
        }
        
        return false;
    }

}

?>