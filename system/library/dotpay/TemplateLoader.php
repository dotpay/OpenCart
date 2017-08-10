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

class TemplateLoader
{
    /**
     * Constructor
     *
     * @param	object	$registry
    */
    public function __construct($registry) {
            $this->registry = $registry;
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
            $template = new \Template($this->registry->get('config')->get('template_engine'));

            foreach ($data as $key => $value) {
                    $template->set($key, $value);
            }

            $output = $template->render($this->registry->get('config')->get('template_directory') . $route, false);//this is very important!	
        }

        // Trigger the post events
        $result = $this->registry->get('event')->trigger('view/' . $trigger . '/after', array(&$route, &$data, &$output));

        if ($result && !$result instanceof \Exception) {
                $output = $result;
        }

        return $output;
    }
}