<?php

/**
 * English language pack for admin page in Dotpay payment plugin
 */
$_['heading_title'] = 'Dotpay';
$_['text_dotpay_new'] = '<a onclick="window.open(\'http://www.dotpay.pl/\');" style="cursor: pointer;"><img src="view/image/payment/dotpay_new.png" align="Dotpay" title="Dotpay" style="border: 1px solid #EEEEEE; width: 96px;" /></a>';
$_['text_home'] = 'Dashboard';
$_['text_payment'] = 'Extensions';
$_['button_save'] = 'Save settings';
$_['button_cancel'] = 'Cancel';
$_['text_success'] = 'Changes have been saved!';
$_['main_header_edit'] = 'Edit this settings';
$_['tab_main'] = 'Main settings';
$_['tab_channels'] = 'Visible of channels';
$_['tab_statuses'] = 'Statuses';
$_['tab_env'] = 'Information';
$_['tab_cards'] = 'Saved cards';

$_['text_dotpay_register'] = 'Register your new account in Dotpay.pl';

$_['text_enabled'] = 'Enabled';
$_['text_disabled'] = 'Disabled';
$_['text_active_status'] = 'Payment module status';

$_['text_dotpay_id'] = 'Shop ID ';
$_['text_dotpay_id_help'] = 'Shop ID in Dotpay should contain 6 digits.';
$_['text_dotpay_id_validate'] = 'Your shop ID should contain 6 digits !';
$_['text_dotpay_pin'] = 'PIN';
$_['text_dotpay_pin_help'] = 'You can get the PIN code from your Dotpay account';
$_['text_dotpay_pin_validate'] = 'PIN shoud contain min 16 and max 32 characters';
$_['text_dotpay_test'] = 'Testing environment';
$_['text_dotpay_test_info'] = 'Required Dotpay test account: <a href="https://ssl.dotpay.pl/test_seller/test/registration/?affilate_id=module_opencart" target="_blank" title="Dotpay test account registration">registration</a>';
$_['text_sort_order'] = 'Sort position of gateway on list in checkout';
$_['text_dotpay_api_info'] = '<h3>API data (optional) </h3>Required for proper operation One Click and display instructions for Transfer channels (wire transfer data are not passed to the bank and a payer needs to copy or write the data manually).';
$_['text_dotpay_username'] = 'Dotpay API username <br><em>(Your username for Dotpay seller panel)</em>';
$_['text_dotpay_password'] = 'Dotpay API password <br><em>(Your password for Dotpay seller panel)</em>';

$_['text_dotpay_oc'] = 'One Click';
$_['text_dotpay_pv'] = 'Special credit card chanel for selected currencies';
$_['text_dotpay_pv_id'] = 'Shop ID for credit channel';
$_['text_dotpay_pv_pin'] = 'PIN for credit channel';
$_['text_dotpay_pv_curr'] = 'Currencies for special credit card channel';
$_['text_dotpay_pv_curr_help'] = 'Please enter currency codes separated by commas, for example: EUR,USD';
$_['text_dotpay_pv_curr_validate'] = 'for example: EUR,USD';
$_['text_dotpay_cc'] = 'Credit card channel';
$_['text_dotpay_mp'] = 'MasterPass channel';
$_['text_dotpay_blik'] = 'BLIK channel';
$_['text_dotpay_widget'] = 'Widget on shop site';

$_['text_dotpay_status_rejected'] = 'Payment rejected status';
$_['text_dotpay_status_completed'] = 'Payment completed status';
$_['text_dotpay_status_processing'] = 'Payment processing status';
$_['text_dotpay_status_return'] = 'Return complete status';

$_['text_dotpay_plugin_version'] = 'Dotpay plugin version';
$_['text_dotpay_plugin_version_check'] = '<a href="https://github.com/dotpay/OpenCart/releases/latest" title="OpenCart Dotpay payment module - check version" target="_blank" >Check</a> for new version of this payment module';
$_['text_dotpay_api_version'] = 'API version';
$_['text_dotpay_ip'] = 'Dotpay IP address';
$_['text_dotpay_office_ip'] = 'Dotpay office IP address';
$_['text_dotpay_URLC'] = 'Notification URLC address';
$_['text_dotpay_URL'] = 'Address return to the store';

$_['ocmanage_card_number'] = 'Card number';
$_['ocmanage_card_brand'] = 'Card brand';
$_['ocmanage_username'] = 'User';
$_['ocmanage_email'] = 'Email';
$_['ocmanage_register_date'] = 'Register date';
$_['ocmanage_deregister'] = 'Deregister';
$_['ocmanage_deregister_card'] = 'Deregister your card from shop';
$_['ocmanage_alert_notfound'] = 'You haven\'t any registered cards';
$_['ocmanage_on_remove_message'] = 'Do you want to deregister a saved card';
$_['ocmanage_on_done_message'] = 'The card was deregistered from shop';
$_['ocmanage_on_failure_message'] = 'An error occurred while deregistering the card';

$_['error_permission'] = 'Warning: You do not have permission to access the API!';
$_['error_dotpay_id'] = 'Enter the ID of the seller!';
$_['error_dotpay_pin'] = 'Enter the PIN!';
$_['error_dotpay_unauthorized_manipulaed'] = 'Unauthorized manipulation of settings';

?>
