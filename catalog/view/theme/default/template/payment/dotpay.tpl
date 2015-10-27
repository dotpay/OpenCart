<style>
    .btn-dotpay {            
       cursor: pointer;
       display: block;
       border: 1px solid #7d1416;
    }  
    .btn-dotpay > button {
        color: white;   
        border-radius: 0;
        background-color: #7d1416;
        background-image: linear-gradient(to bottom, #7d1416, #7d1416);
        border-color: #7d1416 #7d1416 #7d1416;
        height: 25px;
        padding: 0;
        font-size: 11px;        
    }    
    .btn-dotpay > button:hover {
       color: white;   
    }
       
</style>

<form action="<?=$action; ?>" method="<?=$method;?>" id="dotpay" style="display: none">  
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
            <td align="right">                
                <a class="btn-dotpay" onclick="$('form#dotpay').submit();">
                    <button class="btn btn-block">
                        <?=$text_button_confirm; ?>
                    </button>                           
                    <img title="<?=$text_button_confirm; ?>" src="image/dotpay/dotpay.png">
                </a>
            </td>
        </tr>
    </table>
</div>
