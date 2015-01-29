<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltp" lang="pl" xml:lang="pl">
<head>
<title>Transferuj.pl redirect</title>
<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/stylesheet.css" />
<!--[if lt IE 7]>
<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/ie6.css" />
<![endif]-->
</head>
<body>
<form action="https://secure.transferuj.pl/" method="post" id="tr_payment" name="tr_payment">  
<input type="hidden" name="id" value="<?=$seller_id;?>">
<input type="hidden" name="kwota" value="<?=$kwota;?>">
<input type="hidden" name="opis" value="<?=$opis;?>">
<input type="hidden" name="crc" value="<?=$crc;?>">
<input type="hidden" name="wyn_url" value="<?=$wyn_url;?>">
<input type="hidden" name="pow_url" value="<?=$pow_url;?>">
<input type="hidden" name="pow_url_blad" value="<?=$pow_url_blad;?>">
<input type="hidden" name="email" value="<?=$email;?>">
<input type="hidden" name="nazwisko" value="<?=$nazwisko;?>">
<input type="hidden" name="imie" value="<?=$imie;?>">
<input type="hidden" name="adres" value="<?=$adres;?>">
<input type="hidden" name="miasto" value="<?=$miasto;?>">
<input type="hidden" name="kod" value="<?=$kod;?>">
<input type="hidden" name="kraj" value="<?=$kraj;?>">
<input type="hidden" name="telefon" value="<?=$telefon;?>">
<input type="hidden" name="md5sum" value="<?=$md5sum;?>">
<div align="center"><?=$text_transferuj_redirect;?>
<input type="submit" value="<?=$text_transferuj_redirect_btn;?>" /></div>
</form>
<script type="text/javascript">
setTimeout(function(){document.getElementById('tr_payment').submit();}, 2000);
</script>
</body>
</html>