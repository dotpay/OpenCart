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
 *  @copyright Dotpay
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

namespace dotpay;

class RegisterOrder
{
    /**
     * Name of plugin.
     */
    const PLUGIN_NAME = 'dotpay_new';

    /**
     * Registry class with shop environment data.
     *
     * @var Registry
     */
    private $registry;

    public function __construct($registry = array())
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
     * @var string Target url for Register Order
     */
    const target = 'payment_api/v1/register_order/';

    /**
     * Initialize Register Order mechanism.
     */
    public function init()
    {
        self::$parent = $parent;
        self::$config = new DotpayConfig();
    }

    /**
     * Create register order, if it not exist.
     *
     * @param array $orderDetails Data of custom order
     *
     * @return null|array
     */
    public function create($orderDetails)
    {
        $data = str_replace('\\/', '/', json_encode($this->prepareData($orderDetails)));
        if (!$this->checkIfCompletedControlExist($orderDetails['control'], $orderDetails['channel'])) {
            return $this->createRequest($data);
        }

        return null;
    }

    /**
     * Create request without checking conditions.
     *
     * @param array $data
     *
     * @return bool
     */
    private function createRequest($data)
    {
        try {
            $curl = new Curl();
            $curl->addOption(CURLOPT_URL, $this->config->get('payment_'.self::PLUGIN_NAME.'_target_payment_url').self::target)
                 ->addOption(CURLOPT_SSL_VERIFYPEER, true)
                 ->addOption(CURLOPT_SSL_VERIFYHOST, 2)
                 ->addOption(CURLOPT_RETURNTRANSFER, 1)
                 ->addOption(CURLOPT_TIMEOUT, 100)
                 ->addOption(CURLOPT_USERPWD, $this->config->get('payment_'.self::PLUGIN_NAME.'_username').':'.$this->config->get('payment_'.self::PLUGIN_NAME.'_password'))
                 ->addOption(CURLOPT_POST, 1)
                 ->addOption(CURLOPT_POSTFIELDS, $data)
                 ->addOption(CURLOPT_HTTPHEADER, array(
                    'Accept: application/json; indent=4',
                    'content-type: application/json', ));
            $resultJson = $curl->exec();
            $resultStatus = $curl->getInfo();
        } catch (Exception $exc) {
            $resultJson = false;
        }

        if ($curl) {
            $curl->close();
        }

        if (false !== $resultJson && $resultStatus['http_code'] == 201) {
            return json_decode($resultJson, true);
        }

        return false;
    }

    /**
     * Check, if order with id from control field is completed.
     *
     * @param int $control Order id from control field
     * @param int $channel Channel id
     *
     * @return bool
     */
    private function checkIfCompletedControlExist($control, $channel)
    {
        $this->load->library('dotpay/SellerApi');
        $payments = $this->SellerApi->getPaymentByOrderId($this->config->get('payment_'.self::PLUGIN_NAME.'_username'), $this->config->get('payment_'.self::PLUGIN_NAME.'_password'), $control);
        foreach ($payments as $payment) {
            $onePayment = $this->SellerApi->getPaymentByNumber($this->config->get('payment_'.self::PLUGIN_NAME.'_username'), $this->config->get('payment_'.self::PLUGIN_NAME.'_password'), $payment->number);
            if ($onePayment->control == $control && $onePayment->payment_method->channel_id == $channel && $payment->status == 'completed') {
                return true;
            }
        }

        return false;
    }

    /**
     * Prepares the data for query.
     *
     * @param int $orderDetails
     *
     * @return array
     */
    private function prepareData($orderDetails)
    {
        return array(
            'order' => array(
                'amount' => $orderDetails['amount'],
                'currency' => $orderDetails['currency'],
                'description' => $orderDetails['description'],
                'control' => $orderDetails['control'],
            ),

            'seller' => array(
                'account_id' => $orderDetails['id'],
                'url' => $orderDetails['URL'],
                'urlc' => $orderDetails['URLC'],
            ),

            'payer' => array(
                'first_name' => $orderDetails['firstname'],
                'last_name' => $orderDetails['lastname'],
                'email' => $orderDetails['email'],
                'address' => array(
                    'street' => $orderDetails['street'],
                    'building_number' => $orderDetails['street_n1'],
                    'postcode' => $orderDetails['postcode'],
                    'city' => $orderDetails['city'],
                    'country' => $orderDetails['country'],
                ),
            ),

            'payment_method' => array(
                'channel_id' => $orderDetails['channel'],
            ),

            'request_context' => array(
                'ip' => $_SERVER['REMOTE_ADDR'],
            ),
        );
    }
}
