<style>
    .dotpay-oc select[name=card_id] {
        margin-left: 20px;
        margin-bottom: 10px;
    }
    
    .dotpay-oc label {
        line-height: 15px;
        margin-bottom: 8px;
        min-width: 250px;
    }
</style>
<div class="dotpay-oc">
    <div id="dotpay-oc-savedcard-area">
        <label>
            <input type="radio" name="oc_type" value="saved" /><?php echo $label_select_oc_card; ?>
            &nbsp;(<a href="<?php echo $url_see_cards; ?>" target="_blank"><?php echo $text_see_cards; ?></a>)
        </label>
        <select name="card_id">
            <?php foreach($oc_cards as $card): ?>
            <option value="<?php echo $card['cc_id']; ?>"><?php echo $card['mask']; ?>&nbsp;(<?php echo $card['brand']; ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>
    <label>
        <input type="radio" name="oc_type" value="new" /><?php echo $label_register_oc_card; ?>
    </label>
    <label>
        <input type="checkbox" name="oneclick_agreements" required="required" value="1" checked="true"><?php echo $register_card_agreement; ?>
    </label>
</div>