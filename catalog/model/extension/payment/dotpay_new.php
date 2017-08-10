<?php

/**
 * Model of basic operations.
 */
class ModelExtensionPaymentDotpayNew extends Model
{
    /**
     * Name of plugin.
     */
    const PLUGIN_NAME = 'dotpay_new';

    /**
     * Returns details of Dotpay payment gateway, needed by OpenCart tools.
     *
     * @param array $address Array with data of customer's address
     *
     * @return array
     */
    public function getMethod($address)
    {
        $this->load->language('extension/payment/'.self::PLUGIN_NAME);

        $method_data = array(
            'code' => self::PLUGIN_NAME,
            'title' => $this->language->get('text_dotpay_title'),
            'sort_order' => $this->config->get(self::PLUGIN_NAME.'_sort_order'),
            'terms' => '',
        );

        return $method_data;
    }

    /**
     * Returns data of order return.
     *
     * @param int $orderId Order id
     *
     * @return array
     */
    public function getReturnByOrderId($orderId)
    {
        $query = $this->db->query("SELECT DISTINCT *, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM ".DB_PREFIX.'customer c WHERE c.customer_id = r.customer_id) AS customer FROM `'.DB_PREFIX."return` r WHERE r.order_id = '".(int) $orderId."' ORDER BY date_added DESC");

        return $query->row;
    }

    /**
     * Adds new order's return history.
     *
     * @param int   $returnId Return id
     * @param array $data     Data of order's return
     */
    public function addReturnHistory($returnId, $data)
    {
        $this->db->query('UPDATE `'.DB_PREFIX."return` SET return_status_id = '".(int) $data['return_status_id']."', date_modified = NOW() WHERE return_id = '".(int) $returnId."'");

        $this->db->query('INSERT INTO '.DB_PREFIX."return_history SET return_id = '".(int) $returnId."', return_status_id = '".(int) $data['return_status_id']."', notify = '".(isset($data['notify']) ? (int) $data['notify'] : 0)."', comment = '".$this->db->escape(strip_tags($data['comment']))."', date_added = NOW()");
    }
}
