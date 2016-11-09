<?php echo $header; ?>

<?php echo $column_left; ?>

<div class="container">
    <style>
    #instruction {
        text-align: left;
        float: left;
        margin: 0px;
        padding: 0px 10px 10px;
        border: 1px solid #F1F1F1;
        width: 100%;
    }

    #instruction label {
        display: block;
        font-weight: normal;
        font-size: 18px;
        margin-bottom: 15px;
        margin-top: 5px;
    }

    #instruction label > input, #instruction label > .input-group {
        margin-top: 7px;
    }

    #instruction input.important {
        color: #881920;
        font-weight: bold;
        font-size: 18px
    }
    
    #instruction #amount {
        width: 90%;
    }
    
    #instruction .input-group {
        width: 100%;
    }
    
    #instruction #transfer-currency {
        width: 10%;
        height: 34px;
        font-size: 18px;
    }

    #instruction-content {
        font-size: 1.3em;
        margin: 15px 0px 20px;
        text-align: center;
    }

    #blankiet-download-form {
        text-align: center;
        padding: 0px;
        margin: 20px 0px;
    }

    #channel_container_confirm {
        border-radius: 3px;
        display: inline-block;
        margin: 5px 10px;
        border: 1px solid #F0F0F0;
        background-color: #FFF;
        text-align: center;
        width: 200px;
    }

    #channel_container_confirm a {
        width: 100%;
        height: 100%;
    }

    #channel_container_confirm img {
        margin: auto;
    }

    #channel_container_confirm span {
        font-size: 1.3em;
        font-weight: bold;
        display: block;
        margin: 10px;
    }
}
    </style>
    <script type="text/javascript">
        jQuery(document).ready(function(){
            jQuery('#instruction input').keypress(function(e){
                e.preventDefault();
            }).focus(function(e){
                jQuery(this).select();
            });
        });
    </script>
    <?php if($info_order_not_found!==NULL): ?>
    <div class="col-xs-12">
        <p id="instruction-content" class="alert alert-danger"><?php echo $info_order_not_found; ?></p>
    </div>
    <?php else: ?>
    <section id="instruction">
        <div class="row">
            <div class="col-xs-12">
                <p id="instruction-content" class="alert alert-info"><?php echo $info_info; ?></p>
            </div>
            <div class="col-md-6">
                <?php if($bank_account!=NULL): ?>
                <label clas="row">
                    <?php echo $info_account; ?>
                    <input type="text" class="important form-control" id="iban" value="<?php echo $bank_account; ?>" />
                </label>
                <?php endif; ?>
                <label clas="row">
                    <?php echo $info_amount; ?>
                    <div class="input-group">
                        <input type="text" class="important col-md-10 form-control" id="amount" value="<?php echo $amount; ?>" aria-describedby="transfer-currency">
                        <span class="input-group-addon col-md-2" id="transfer-currency"><?php echo $currency; ?></span>
                    </div>
                </label>
                <label clas="row">
                    <?php echo $info_title; ?>
                    <input type="text" class="important form-control" id="payment-title" value="<?php echo $title; ?>" />
                </label>
            </div>
            <div class="col-md-6">
                <label clas="row">
                    <?php echo $info_name; ?>
                    <input type="text" class="important form-control" id="recipient" value="<?php echo $name; ?>" />
                </label>
                <label clas="row">
                    <?php echo $info_street; ?>
                    <input type="text" class="important form-control" id="street" value="<?php echo $street; ?>" />
                </label>
                <label clas="row">
                    <?php echo $info_postcode; ?>
                    <input type="text" class="important form-control" id="post-code-city" value="<?php echo $postcode; ?>" />
                </label>
            </div>
        </div>
        <div class="row">
            <section id="payment-form" class="col-xs-12">
                <div id="blankiet-download-form">
                    <div id="channel_container_confirm">
                        <a href="<?php echo $address; ?>" target="_blank" title="<?php echo $command; ?>">
                            <div>
                                <img src="<?php echo $logo; ?>" alt="<?php echo $info_logo; ?>" />
                                <span><?php echo $command; ?></span>
                            </div>
                        </a>
                    </div>
                </div>
            </section>
            <div class="col-xs-12">
                <p class="alert alert-warning"><?php echo $info_warning; ?></p>
            </div>
        </div>
    </section>
    <?php endif; ?>
</div>

<?php echo $footer; ?>