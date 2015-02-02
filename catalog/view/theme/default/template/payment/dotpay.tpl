<form action="<?=$action; ?>" method="<?=$method;?>" id="dotpay" style="display: none" target="_blank">  
    <input type="hidden" name="id" value="<?=$dotpay['id'];?>">    
    <input type="hidden" name="amount" value="<?=$dotpay['amount'];?>">    
    <input type="hidden" name="currency" value="<?=$dotpay['currency'];?>">    
    <input type="hidden" name="lang" value="<?=$dotpay['lang'];?>">    
    <input type="hidden" name="description" value="<?=$dotpay['description'];?>">    
    <input type="hidden" name="p_info" value="<?=$dotpay['p_info'];?>">    
    <input type="hidden" name="p_email" value="<?=$dotpay['p_email'];?>">    
    <input type="hidden" name="control" value="<?=$dotpay['control'];?>">    
    <input type="hidden" name="URL" value="<?=$dotpay['URL'];?>">    
    <input type="hidden" name="URLC" value="<?=$dotpay['URLC'];?>">    
    <input type="hidden" name="type" value="<?=$dotpay['type'];?>">    
    <input type="hidden" name="api_version" value="<?=$dotpay['api_version'];?>">    
    <input type="hidden" name="lastname" value="<?=$dotpay['lastname'];?>">    
    <input type="hidden" name="firstname" value="<?=$dotpay['firstname'];?>">    
    <input type="hidden" name="email" value="<?=$dotpay['email'];?>">    
    
</form>
<div class="buttons pull-right">
    <table>
        <tr>  
            <!--<td align="left"><a onclick="location = '<?=$back; ?>'" class="button btn"><span><i class="fa fa-arrow-left"></i> <?=$text_button_back; ?></span></a></td>-->
            <td align="right"><a onclick="$('form#dotpay').submit();" class="btn btn-primary"><?=$text_button_confirm; ?></a></td>
        </tr>
    </table>
</div>
