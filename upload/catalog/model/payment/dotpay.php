<?php 
class ModelPaymentDotpay extends Model
{
	public function getMethod($address)
	{
		$this->load->language('payment/dotpay');
		
		if($this->config->get('dotpay_status'))
		{
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('dotpay_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
			
			if(!$this->config->get('dotpay_geo_zone_id'))
			{
				$status = true;
			}
			elseif($query->num_rows)
			{
				$status = true;
			}
			else
			{
				$status = false;
			}
		}
		else
		{
			$status = false;
		}
		
		$method_data = array();
		
		if($status)
		{  
			$method_data = array( 
				'code' => 'dotpay',
				'title' => $this->language->get('text_title'),
				'sort_order' => $this->config->get('dotpay_sort_order'),
                                'terms'=>'');
		}
		
		return $method_data;
	}
    
    public function getReturnByOrderId($order_id)
	{
        $query = $this->db->query("SELECT DISTINCT *, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = r.customer_id) AS customer FROM `" . DB_PREFIX . "return` r WHERE r.order_id = '" . (int)$order_id . "' ORDER BY date_added DESC");

		return $query->row;
    }
    
    public function addReturnHistory($return_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "return` SET return_status_id = '" . (int)$data['return_status_id'] . "', date_modified = NOW() WHERE return_id = '" . (int)$return_id . "'");

		$this->db->query("INSERT INTO " . DB_PREFIX . "return_history SET return_id = '" . (int)$return_id . "', return_status_id = '" . (int)$data['return_status_id'] . "', notify = '" . (isset($data['notify']) ? (int)$data['notify'] : 0) . "', comment = '" . $this->db->escape(strip_tags($data['comment'])) . "', date_added = NOW()");
		
	}
    
    public function addTransaction($customer_id, $description = '', $amount = '', $order_id = 0) {
        
		$customer = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'")->row;
        
		if ($customer) {
			
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer_transaction SET " .
                    "customer_id = '" . (int)$customer_id .
                    "', order_id = '" . (int)$order_id .
                    "', description = '" . $this->db->escape($description) .
                    "', amount = '" . (float)$amount .
                    "', date_added = NOW()");			
		}
	}
    
    
}
?>