<?php echo $header; ?>

<script type="text/javascript">
    function enabledEdit(selector){
        input = selector.closest('.input-group').find('input');
        input.prop("readonly", false);       
    }
</script>

<head><meta http-equiv="content-type" content="text/html; charset=utf-8"></head>
<?php echo $column_left; ?>

<div id="content">
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
            <?php if ($error['permision']) { ?>
            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error['permision']; ?>
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
                                <?php if ($error['dotpay_id']) { ?>
                                    <small class="text-danger"><?=$error['dotpay_id']; ?></small>
                                <?php } ?>
                            </div>                          
                        </div>         
                        <?php /*
                      <div class="form-group">
                            <label class="col-sm-2 control-label"> <?=$text_dotpay_currency; ?></label>
                            <div class="col-sm-10">
                                <select name="dotpay_currency" class="form-control" id="dotpay_currency">
                                    <?php foreach ($currencies as $curr) { ?>
                                    <option value="<?=$curr['code']; ?>"<?=($dotpay_currency == $curr['code'] ? ' selected="selected"' : ''); ?>><?=$curr['code']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>		
                        </div> */ ?>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" ><span class="required" data-toggle="tooltip" title="<?=$text_dotpay_pin_help; ?>"></span> <?=$text_dotpay_pin; ?></label>
                            <div class="col-sm-10" >
                                <input  class="form-control" size="32" maxlength="32" name="dotpay_pin" value="<?=$dotpay_pin; ?>" /> 
                                <?php if ($error['dotpay_pin']) { ?>
                                    <small class="text-danger"><?=$error['dotpay_pin']; ?></small>
                                <?php } ?>
                            </div>                            
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?=$text_dotpay_status_rejected; ?></label>
                            <div class="col-sm-10">
                                <select class="form-control" name="dotpay_status_rejected"><?php
                                    foreach ($order_statuses as $status) {
                                    echo'<option value="'.$status['order_status_id'].'"'.($status['order_status_id'] == $dotpay_status_rejected ? ' selected="selected"' : '').'>'.$status['name'].'</option>';
                                    }?>
                                </select>
                            </div>
                        </div>                        

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?=$text_dotpay_status_completed; ?></label>
                            <div class="col-sm-10">
                                <select class="form-control" name="dotpay_status_completed"><?php
                                    foreach ($order_statuses as $status) {
                                    echo'<option value="'.$status['order_status_id'].'"'.($status['order_status_id'] == $dotpay_status_completed ? ' selected="selected"' : '').'>'.$status['name'].'</option>';
                                    }?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?=$text_dotpay_status_processing; ?></label>
                            <div class="col-sm-10">
                                <select class="form-control" name="dotpay_status_processing"><?php
                                    foreach ($order_statuses as $status) {
                                    echo'<option value="'.$status['order_status_id'].'"'.($status['order_status_id'] == $dotpay_status_processing ? ' selected="selected"' : '').'>'.$status['name'].'</option>';
                                    }?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?=$text_dotpay_return_status_completed; ?></label>
                            <div class="col-sm-10">
                                <select class="form-control" name="dotpay_return_status_completed"><?php
                                    foreach ($return_statuses as $status) {
                                    echo'<option value="'.$status['return_status_id'].'"'.($status['return_status_id'] == $dotpay_return_status_completed ? ' selected="selected"' : '').'>'.$status['name'].'</option>';
                                    }?>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?=$text_dotpay_ip; ?></label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input class="form-control" readonly type="text" name="dotpay_ip" value="<?=$dotpay_ip;?>" size="16" maxlength="16" />
                                    <span class="input-group-btn">
                                        <button onclick="enabledEdit($(this))" class="btn btn-primary" type="button"><?=$button_edit; ?></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?=$text_dotpay_request_url; ?></label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input class="form-control" readonly type="text" name="dotpay_request_url" value="<?=$dotpay_request_url; ?>" />
                                    <span class="input-group-btn">
                                        <button onclick="enabledEdit($(this))" class="btn btn-primary" type="button"><?=$button_edit; ?></button>
                                    </span>
                                </div>
                            </div>                            
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?=$text_dotpay_URL; ?></label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1"><?=HTTPS_SERVER; ?></span>
                                    <input class="form-control" readonly type="text" name="dotpay_URL" value="<?=$dotpay_URL; ?>"/>    
                                    <span class="input-group-btn">
                                        <button onclick="enabledEdit($(this))" class="btn btn-primary" type="button"><?=$button_edit; ?></button>
                                    </span>
                                </div>
                            </div>
                        </div>                          
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?=$text_dotpay_URLC; ?></label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1"><?=HTTPS_SERVER; ?></span>
                                    <input class="form-control" readonly type="text" name="dotpay_URLC" value="<?=$dotpay_URLC; ?>"/>    
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="dotpay_request_method" value="<?=$dotpay_request_method; ?>" />
                    <input type="hidden" name="dotpay_api_version" value="<?=$dotpay_api_version; ?>" />
                    <input type="hidden" name="dotpay_type" value="<?=$dotpay_type; ?>" />
                </form>
            </div>
        </div>
    </div>
</div>



<?=$footer; ?>