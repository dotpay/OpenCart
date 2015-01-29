<?php echo $header; ?>

<script type="text/javascript">
    $(document).ready(function () {
        jQuery("#miejsce").change(function () {


            if ($("#miejsce option:selected").val() == "1") {
                
                  $("#wyglad").attr("style", "visibility: hidden")

            }
            else {
              
                $("#wyglad").attr("style", "visibility: ")

            }

        });


    });
</script>

<head><meta http-equiv="content-type" content="text/html; charset=utf-8"></head>
<?php echo $column_left; ?>

<div id="content">
    <?php if ($error_warning) { ?>
    <div class="warning"><?=$error_warning; ?></div>
    <?php } ?>

    <div class="container-fluid">

        <div class="page-header">
            <div class="container-fluid">
                <div class="pull-right">
                    <button type="submit" form="form" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                    <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
                <h1><?php echo $heading_title; ?></h1>
                <ul class="breadcrumb">
                    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="content">
            <?php if ($error_permission) { ?>
            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_permission; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            <?php } ?>
            <div class="panel panel-default" width="360%">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i><?php echo $text_edit; ?></h3>
                </div>
                <form action="<?=$action; ?>" method="post" enctype="multipart/form-data" id="form">
                    <div style="margin: 17px 18px 27px 47px; font-size: 15px; " class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?=$text_active_status; ?></label>
                            <div class="col-sm-10">
                                <select class="form-control" name="dotpay_status">
                                    <option value="1"><?=$text_enabled; ?></option>
                                    <option value="0"<?=(!$dotpay_status ? ' selected="selected"' : '' ); ?>><?=$text_disabled; ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?=$text_sort_order; ?></label>
                            <div class="col-sm-10"><input class="form-control" type="number" name="dotpay_sort_order" value="<?=$dotpay_sort_order; ?>"/></div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?=$text_dotpay_id; ?></label>                           
                            <div class="col-sm-10">
                                <input class="form-control" type="number" name="dotpay_id" value="<?=$dotpay_id; ?>" />
                                <?php if ($error_id) { ?>
                                    <small class="text-danger"><?=$error_id; ?></small>
                                <?php } ?>
                            </div>                          
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?=$text_dotpay_ip; ?></label>
                            <div class="col-sm-10">
                                <input class="form-control" type="text" name="dotpay_ip" value="<?=(empty($transferuj_ip) ? '195.149.229.109' : $transferuj_ip); ?>" size="16" maxlength="16" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"> <?=$text_dotpay_currency; ?></label>
                            <div class="col-sm-10">
                                <select name="dotpay_currency" class="form-control" id="dotpay_currency">
                                    <?php foreach ($curr as $name) { ?>
                                    <option value="<?=$name; ?>"<?=($dotpay_currency == $name ? ' selected="selected"' : ''); ?>><?=$name; ?></option>
                                    <?php } ?>
                                </select>
                            </div>		
                        </div>  
                        <div class="form-group">
                            <label class="col-sm-2 control-label" ><span class="required" data-toggle="tooltip" title="<?=$text_dotpay_pin_help; ?>"></span> <?=$text_dotpay_pin; ?></label>
                            <div class="col-sm-10" >
                                <input  class="form-control" size="32" maxlength="32" name="dotpay_pin" value="<?=$dotpay_pin; ?>" /> 
                                <?php if ($error_pin) { ?>
                                    <small class="text-danger"><?=$error_pin; ?></small>
                                <?php } ?>
                            </div>
                            
                        </div>
<!--                      

                 
                       



                        
                        

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><strong><?=$entry_settings_orders; ?></strong></label>

                        </div>

                        
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?=$entry_transferuj_order_status_error; ?></label>
                            <div class="col-sm-10"><select class="form-control" name="transferuj_order_status_error"><?php
                                    foreach ($order_statuses as $order_status) {
                                    echo'<option value="'.$order_status['order_status_id'].'"'.($order_status['order_status_id'] == $transferuj_order_status_error ? ' selected="selected"' : '').'>'.$order_status['name'].'</option>';
                                    }?></select></div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?=$entry_transferuj_order_status_completed; ?></label>
                            <div class="col-sm-10"><select class="form-control" name="transferuj_order_status_completed"><?php
                                    foreach ($order_statuses as $order_status) {
                                    echo'<option value="'.$order_status['order_status_id'].'"'.($order_status['order_status_id'] == $transferuj_order_status_completed ? ' selected="selected"' : '').'>'.$order_status['name'].'</option>';
                                    }?></select></div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><strong><?=$entry_view_settings; ?></strong></label>

                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"> <?=$entry_transferuj_payment_place; ?></label>

                            <div class="col-sm-10"><select id="miejsce" class="form-control" name="transferuj_payment_place">
                                    <option value="0"><?=$entry_transferuj_payment_place_0; ?></option>
                                    <option value="1"<?=($transferuj_payment_place ? ' selected="selected"' : '' ); ?>><?=$entry_transferuj_payment_place_1; ?></option>
                                </select></div>	

                        </div>

                        <div class="form-group" id="wyglad" <?=($transferuj_payment_place ? ' style="visibility: hidden"' : '' ); ?>>
                            <label class="col-sm-2 control-label"> <?=$entry_transferuj_payment_view; ?></label>

                            <div class="col-sm-10"><select  class="form-control" name="transferuj_payment_view">
                                    <option value="0"><?=$entry_transferuj_payment_view_0; ?></option>
                                    <option value="1"<?=($transferuj_payment_view ? ' selected="selected"' : '' ); ?>><?=$entry_transferuj_payment_view_1; ?></option>
                                </select></div>	

                        </div>-->


                    </div>


            </div>





            </form>
        </div>
    </div>
</div>



<?=$footer; ?>