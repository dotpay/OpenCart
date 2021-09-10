<?php

/**
 * Polish language pack for admin page in Dotpay payment plugin.
 */
$_['code'] = 'pl';
$_['heading_title'] = 'Dotpay';
$_['text_dotpay_next'] = '<a onclick="window.open(\'http://www.dotpay.pl/\');" style="cursor: pointer;"><img src="view/image/payment/dotpay_next.png" title="Dotpay" style="border: 1px solid #EEEEEE; width: 96px;" /></a>';
$_['text_home'] = 'Pulpit';
$_['text_payment'] = 'Rozszerzenia';
$_['button_save'] = 'Zapisz ustawienia';
$_['button_cancel'] = 'Anuluj';
$_['text_success'] = 'Zmiany zostały zapisane!';
$_['main_header_edit'] = 'Edytuj ustawienia';
$_['tab_main'] = 'Główne ustawienia';
$_['tab_channels'] = 'Widoczność kanałów';
$_['tab_statuses'] = 'Statusy';
$_['tab_env'] = 'Informacje';
$_['tab_cards'] = 'Zapisane karty';

$_['text_dotpay_register'] = 'Zarejestruj nowe konto w Dotpay.pl';

$_['text_enabled'] = 'Włączony';
$_['text_disabled'] = 'Wyłączony';
$_['text_active_status'] = 'Status modułu płatności';

$_['text_dotpay_id'] = 'ID sklepu';
$_['text_dotpay_id_help'] = 'ID sklepu w Dotpay powinno zawierać 6 cyfr.';
$_['text_dotpay_id_validate'] = 'Twój ID sklepu powinien zawierać 6 cyfr';
$_['text_dotpay_pin'] = 'PIN';
$_['text_dotpay_pin_help'] = 'Możesz znaleźć swój PIN w ustawieniach konta w panelu klienta Dotpay';
$_['text_dotpay_pin_validate'] = 'PIN powinien zawierać od 16 do 32 znaków';
$_['text_dotpay_test'] = 'Środowisko testowe';
$_['text_dotpay_test_info'] = 'Wymagane osobne konto testowe. <a href="https://www.dotpay.pl/developer/sandbox/pl/?affilate_id=module_opencart3" target="_blank" title="Uzyskaj konto testowe w Dotpay">Utwórz konto testowe</a>';
$_['text_dotpay_nonproxy'] = 'Mój serwer nie korzysta z proxy';
$_['text_dotpay_nonproxy_info'] = 'Domyślnie zalecamy ustawić włączone (bez proxy).<br/>Jeśli jesteś pewien że Twój serwer korzysta z proxy, lub masz problemy z odbiorem potwierdzeń o dokończonej płatności - wyłącz to.';
$_['text_sort_order'] = 'Pozycja bramki na liście płatności';
$_['text_dotpay_api_info'] = '<h3>Dane API (opcjonalnie) </h3>Wymagane dla poprawnego działania One Click oraz wyświetlenia na stronie sklepu instrukcji płatniczych dla kanałów nietransferowych.';
$_['text_dotpay_username'] = 'Nazwa użytkownika API <br><em>(Login do panelu sprzedawcy Dotpay)</em>';
$_['text_dotpay_password'] = 'Hasło API br><em>(Hasło do panelu sprzedawcy Dotpay)</em>';

$_['text_dotpay_oc'] = 'One Click';
$_['text_dotpay_pv'] = 'Specjalny kanał kredytowy dla wybranych walut';
$_['text_dotpay_pv_id'] = 'ID sklepu dla kanału kartowego';
$_['text_dotpay_pv_pin'] = 'PIN dla kanału kartowego';
$_['text_dotpay_pv_curr'] = 'Waluty dla specjalnego kanału kartowego';
$_['text_dotpay_pv_curr_help'] = 'Proszę wpisać kody walut, oddzielone przecinkami, na przykład: EUR,USD';
$_['text_dotpay_pv_curr_validate'] = 'na przykład: EUR,USD';
$_['text_dotpay_cc'] = 'Kanał kart płatniczych';
$_['text_dotpay_mp'] = 'Kanał MasterPass';
$_['text_dotpay_blik'] = 'Kanał Blik';
$_['text_dotpay_widget'] = 'Widget na stronie sklepu';

$_['text_dotpay_status_rejected'] = 'Status odrzuconej płatności';
$_['text_dotpay_status_completed'] = 'Status potwierdzonej płatności';
$_['text_dotpay_status_processing'] = 'Status płatności w trakcie przetwarzania';
$_['text_dotpay_status_return'] = 'Status potwierdzonego zwrotu';

$_['text_dotpay_plugin_version'] = 'Wersja pluginu Dotpay';
$_['text_dotpay_plugin_version_check'] = '<a href="https://github.com/dotpay/OpenCart/releases/latest" title="Wtyczka dla OpenCart dodająca bramkę płatności Dotpay - sprawdź czy jest nowa wersja" target="_blank">Sprawdź/a> czy jest dostępna nowa wersja tej wtyczki';
$_['text_dotpay_api_version'] = 'Wersja API';
$_['text_dotpay_ip'] = 'Adres IP Dotpay';
$_['text_dotpay_office_ip'] = 'Adres IP biura';
$_['text_dotpay_URLC'] = 'Adres do powiadomień URLC';
$_['text_dotpay_URL'] = 'Adres powrotu do sklepu';

$_['ocmanage_card_number'] = 'Numer karty';
$_['ocmanage_card_brand'] = 'Marka karty';
$_['ocmanage_username'] = 'Użytkownik';
$_['ocmanage_email'] = 'Email';
$_['ocmanage_register_date'] = 'Data rejestracji';
$_['ocmanage_deregister'] = 'Wyrejestruj';
$_['ocmanage_deregister_card'] = 'Wyrejestruj kartę ze sklepu';
$_['ocmanage_alert_notfound'] = 'Nie masz zarejestrowanych żadnych kart';
$_['ocmanage_on_remove_message'] = 'Czy chcesz wyrejestrować zapisaną kartę';
$_['ocmanage_on_done_message'] = 'Karta została wyrejestrowana ze sklepu';
$_['ocmanage_on_failure_message'] = 'Wystapił błąd podczas wyrejestrowania karty';

$_['error_permission'] = 'Uwaga: nie masz uprawnień modyfikacji ustawień.';
$_['error_dotpay_id'] = 'Wprowadź ID sprzedawcy!';
$_['error_dotpay_pin'] = 'Wprowadź PIN!';
$_['error_dotpay_unauthorized_manipulaed'] = 'Nieautoryzowana zmiana danych';
