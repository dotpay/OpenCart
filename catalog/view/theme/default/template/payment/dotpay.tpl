<form action="<?=$action; ?>" method="post" id="checkout">  
<input type="hidden" name="cartId" value="<?=$order_id;?>" />
</form>
  <div class="buttons">
    <table>
      <tr>  
          <td align="left"><a onclick="location = '<?=$back; ?>'" class="button btn"><span><i class="fa fa-arrow-left"></i> <?=$button_back; ?></span></a></td>
        <td align="right"><a onclick="$('#checkout').submit();" class="button btn"><span><?=$button_confirm; ?> <i class="fa fa-arrow-right"></i></span></a></td>
      </tr>
    </table>
  </div>
