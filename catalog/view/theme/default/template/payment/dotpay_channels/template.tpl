<div class="dotpay-one-channel dotpay-channel-<?php echo $name; ?>">
    <div class="dotpay-channel-head">
        <label>
            <input type="radio" class="dotpay-channel-switch" name="dotpay-channel-type" value="<?php echo $name; ?>" />
            <img src="image/payment/dotpay/<?php echo $name; ?>.png" alt="<?php echo $title; ?>" title="<?php echo $title; ?>" />
            <?php echo $title; ?>
        </label>
    </div>
    <div class="dotpay-channel-body">
        <?php echo $content; ?>
        <?php if(!isset($no_agreements)): ?>
        <div class="dotpay-agreements">
            <label>
                <input type="checkbox" name="bylaw" required="required" value="1" checked="true"><?php echo $bylaw; ?>
            </label>
            <label>
                <input type="checkbox" name="personal_data" required="required" value="1" checked="true"><?php echo $personal_data; ?>
            </label>
        </div>
        <?php endif; ?>
    </div>
</div>
