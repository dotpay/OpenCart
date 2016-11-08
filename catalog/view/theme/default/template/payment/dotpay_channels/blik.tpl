<style>
    input[name=blik_code] {
        color: #302625;
        font-weight: bold;
        border: 2px solid #302625;
        border-radius: 5px;
        line-height: 25px;
        margin-left: 10px;
    }
</style>
<label>
    <?php echo $blik_code_label; ?><input type="text" maxlength="6" name="blik_code" value="" required="required" placeholder="" pattern="[0-9]{6}" title="<?php echo $blik_validate; ?>" />
</label>