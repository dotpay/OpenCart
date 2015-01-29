<?php

class ControllerPaymentDotpay extends Controller {

    
    public function index() {
        
//        $this->id = 'payment';       
    
        
        $this->load->model('checkout/order');
        $this->load->library('encryption');
        $this->load->language('payment/dotpay');
        
        $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);       
        $data['order_id'] = $order['order_id'];        
        
        $data['text_button_confirm'] = $this->language->get('text_button_confirm');        
//        $data['text_lang'] = $this->language->get('text_lang');
//        $data['text_bank_choice'] = $this->language->get('text_bank_choice');
//        $data['text_accept_terms'] = $this->language->get('text_accept_terms');
        
        $data['dotpay'] = $this->geParams($order);
        
        $data['action'] = 'https://ssl.dotpay.pl/test_payment/';
        $data['method'] = 'GET';
       
        
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
        $data['api_version'] = 'dev';
        
        //optional
        $data['URL'] = HTTPS_SERVER . 'index.php?route=payment/dotpay/confirm'; 
        $data['URLC'] = HTTPS_SERVER . 'index.php?route=payment/dotpay/validate'; 
        $data['type'] = 0;
        
        
        return $data;
    }

    public function pay() {
       echo 'aqweqwesdassssd';
        return;
        $this->response->redirect('https://ssl.dotpay.pl/test_payment/');
        return;
        $this->load->library('encryption');
        $this->load->model('checkout/order');
        $this->load->language('payment/dotpay');
        $order_data = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $this->id = 'payment';
        
//        $param[];
        
        $transferuj_currency = $this->config->get('dotpay_currency');
        $transferuj_currency = 'PLN';

        $transferuj_seller_id = $this->config->get('transferuj_seller_id');
        $transferuj_conf_code = $this->config->get('transferuj_conf_code');
       

        $crc = base64_encode($order_data['order_id']);

        $amount = number_format($this->currency->format($order_data['total'], $transferuj_currency, $order_data['currency_value'], FALSE), 2, '.', '');
        $from = $this->currency->getCode();
        $amount = $this->currency->convert($amount, $from, $transferuj_currency);

        $data['seller_id'] = $transferuj_seller_id;
        $data['kwota'] = $amount;
        $data['opis'] = $this->language->get('text_order') . $order_data['order_id'];
        $data['email'] = $order_data['email'];
        $data['nazwisko'] = $order_data['payment_lastname'];
        $data['imie'] = $order_data['payment_firstname'];
        $data['adres'] = $order_data['payment_address_1'] . $order_data['payment_address_2'];
        $data['miasto'] = $order_data['payment_city'];
        $data['kraj'] = $order_data['payment_country'];
        $data['kod'] = $order_data['payment_postcode'];
        $data['crc'] = $crc;
        $data['jezyk'] = $this->language->get('code');
        $data['md5sum'] = md5($transferuj_seller_id . $amount . $crc . $transferuj_conf_code);
        $data['telefon'] = $order_data['telephone'];
        $data['pow_url'] = HTTPS_SERVER . 'index.php?route=checkout/success';
        $data['pow_url_blad'] = HTTPS_SERVER . 'index.php?route=checkout/checkout';
        $data['wyn_url'] = HTTPS_SERVER . 'index.php?route=payment/transferuj/validate';
        if(isset($this->request->post['kanal'])){
        $data['kanal'] = $this->request->post['kanal'];}
        $data['akceptuje_regulamin'] = isset($this->request->post['akceptuje_regulamin']) ? 1 : 0;

     

        $data['text_transferuj_redirect'] = $this->language->get('text_transferuj_redirect');
        $data['text_transferuj_redirect_btn'] = $this->language->get('text_transferuj_redirect_btn');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/transferuj_redirect.tpl')) {

            $view = $this->load->view($this->config->get('config_template') . '/template/payment/transferuj_redirect.tpl', $data);
            print_r($view);
        } else {

            return $this->load->view('default/template/payment/transferuj_redirect.tpl', $data);
        }
    }
    
    public function confirm(){
        error_log("DOTPAY-POST: " );
        echo 'Potwierdzenie:' . $this->request->get['status'];        
       
    }

    public function validate() {
        error_log("DOTPAY-POST: " );
         foreach ($_POST as $key=>$value){
            error_log("DOTPAY-POST: ".$key . ":" . $value );
        }
        echo 'OK';
        return false;
        if ($_SERVER["REMOTE_ADDR"] != $this->config->get('transferuj_ip'))
            return false;

        echo "TRUE";

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

}

?>