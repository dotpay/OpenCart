<?php echo $header; ?>

<?php echo $column_left; ?>

<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-dotpay" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary">
                    <i class="fa fa-save"></i>
                </button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default">
                    <i class="fa fa-reply"></i>
                </a>
            </div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
            <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
            <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if (isset($error['permission'])) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error['permission']; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } if ($success_msg) { ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success_msg; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-gear"></i> <?php echo $main_header_edit; ?></h3>
            </div>
            <div class="panel-heading">
                <p align="center">
                    <br/>
                    <a href="http://dotpay.pl/" target="_blank" />
                    <img align="Dotpay" style="border: 0;" title="Dotpay" src="../image/payment/<?php echo $plugin_name; ?>.png" />
                    </a>
                </p>
                <?php if (empty($dotpay_id)) { ?>
                    <p align="center">
                        <a href="https://ssl.dotpay.pl/s2/login/registration/?affilate_id=module_opencart" target="_blank" class="btn btn-primary btn-lg" title="<?php echo $text_dotpay_register; ?>">
                            <i class="fa fa-plus"></i>
                            <b><?php echo $text_dotpay_register; ?></b>
                        </a>
                    </p>
                <br/>
                <?php } ?>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-dotpay" class="form-horizontal">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#<?php echo $plugin_name; ?>-tab-main" data-toggle="tab"><i class="fa fa-home" aria-hidden="true"></i>&nbsp;<?php echo $tab_main; ?></a></li>
                        <li><a href="#<?php echo $plugin_name; ?>-tab-channels" data-toggle="tab"><i class="fa fa-ban" aria-hidden="true"></i>&nbsp;<?php echo $tab_channels; ?></a></li>
                        <li><a href="#<?php echo $plugin_name; ?>-tab-statuses" data-toggle="tab"><i class="fa fa-shopping-cart" aria-hidden="true"></i>&nbsp;<?php echo $tab_statuses; ?></a></li>
                        <li><a href="#<?php echo $plugin_name; ?>-tab-cards" data-toggle="tab"><i class="fa fa-credit-card" aria-hidden="true"></i>&nbsp;<?php echo $tab_cards; ?></a></li>
                        <li><a href="#<?php echo $plugin_name; ?>-tab-env" data-toggle="tab"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<?php echo $tab_env; ?></a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="<?php echo $plugin_name; ?>-tab-main">
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php if($dotpay_status != '1'){echo "<font color='red'>";}else{echo "<font color='green'>";}; ?><?php echo $text_active_status; ?></font></label>
                                <div class="col-sm-6">
                                    <select class="form-control" name="<?php echo $plugin_name; ?>_status" style="width: auto !important;">
                                        <option value="1"<?php echo ($dotpay_status ? ' selected="selected"' : '' ); ?>><?php echo $text_enabled; ?></option>
                                        <option value="0"<?php echo (!$dotpay_status ? ' selected="selected"' : '' ); ?>><?php echo $text_disabled; ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><span class="required" data-toggle="tooltip" title="<?php echo $text_dotpay_id_help; ?>"></span> <?php echo $text_dotpay_id; ?></label>                           
                                <div class="col-sm-6">
                                    <input class="form-control" type="text" name="<?php echo $plugin_name; ?>_id" maxlength="6" size="6" value="<?php echo $dotpay_id; ?>" pattern="[0-9]{6}" title="<?php echo $text_dotpay_id_validate; ?>" style="width: auto !important;" />
                                    <?php if (isset($error['dotpay_id'])) { ?>
                                        <small class="text-danger"><?php echo $error['dotpay_id']; ?></small>
                                    <?php } ?>
                                </div>                          
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><span class="required" data-toggle="tooltip" title="<?php echo $text_dotpay_pin_help; ?>"></span> <?php echo $text_dotpay_pin; ?></label>                           
                                <div class="col-sm-6">
                                    <input class="form-control" type="text" name="<?php echo $plugin_name; ?>_pin" size="32" maxlength="32"  value="<?php echo $dotpay_pin; ?>" pattern="[0-9A-Za-z]{16,32}" title="<?php echo $text_dotpay_pin_validate; ?>" style="width: auto !important;" />
                                    <?php if (isset($error['dotpay_pin'])) { ?>
                                        <small class="text-danger"><?php echo $error['dotpay_pin']; ?></small>
                                    <?php } ?>
                                </div>                          
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="<?php echo $plugin_name; ?>_test"><?php echo $text_dotpay_test; ?></label>
                                <div class="col-sm-6">
                                    <select name="<?php echo $plugin_name; ?>_test" id="<?php echo $plugin_name; ?>_test" class="form-control" style="width: auto !important;">
                                        <option value="1"<?php if($dotpay_test) { ?> selected="selected"<?php } ?>><?php echo $text_enabled; ?></option>
                                        <option value="0"<?php if(!$dotpay_test) { ?> selected="selected"<?php } ?>><?php echo $text_disabled; ?></option>
                                    </select>
                                    <?php echo $text_dotpay_test_info; ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $text_sort_order; ?></label>
                                <div class="col-sm-6">
                                    <input class="form-control" type="number" name="<?php echo $plugin_name; ?>_sort_order" value="<?php if($dotpay_sort_order ==''){echo '1';}else{ echo $dotpay_sort_order;}; ?>" style="width: auto !important;" />
                                </div>
                            </div>
                            <div class="form-group" style="background-color: #f9f9f9">
                                <br>
                                <?php echo $text_dotpay_api_info; ?>
                            </div>
                            <div class="form-group" style="background-color: #f9f9f9">
                                <label class="col-sm-3 control-label"><?php echo $text_dotpay_username; ?></label>
                                <div class="col-sm-6">
                                    <input class="form-control" type="text" name="<?php echo $plugin_name; ?>_username" value="<?php echo $dotpay_username;?>" style="width: auto !important;" />                                                                  
                                </div>
                            </div>
                            <div class="form-group" style="background-color: #f9f9f9">
                                <label class="col-sm-3 control-label"><?php echo $text_dotpay_password; ?></label>
                                <div class="col-sm-6">
                                    <input class="form-control" type="password" name="<?php echo $plugin_name; ?>_password" value="<?php echo $dotpay_password;?>" style="width: auto !important;" />                                                                  
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane" id="<?php echo $plugin_name; ?>-tab-channels">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="<?php echo $plugin_name; ?>_oc"><?php echo $text_dotpay_oc; ?></label>
                                <div class="col-sm-10">
                                    <select name="<?php echo $plugin_name; ?>_oc" id="<?php echo $plugin_name; ?>_oc" class="form-control" style="width: auto !important;">
                                        <option value="1"<?php if($dotpay_oc) { ?> selected="selected"<?php } ?>><?php echo $text_enabled; ?></option>
                                        <option value="0"<?php if(!$dotpay_oc) { ?> selected="selected"<?php } ?>><?php echo $text_disabled; ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="<?php echo $plugin_name; ?>_pv"><?php echo $text_dotpay_pv; ?></label>
                                <div class="col-sm-10">
                                    <select name="<?php echo $plugin_name; ?>_pv" id="<?php echo $plugin_name; ?>_pv" class="form-control" style="width: auto !important;">
                                        <option value="1"<?php if($dotpay_pv) { ?> selected="selected"<?php } ?>><?php echo $text_enabled; ?></option>
                                        <option value="0"<?php if(!$dotpay_pv) { ?> selected="selected"<?php } ?>><?php echo $text_disabled; ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group pv-details">
                                <label class="col-sm-3 control-label"><span class="required" data-toggle="tooltip" title="<?php echo $text_dotpay_id_help; ?>"></span> <?php echo $text_dotpay_pv_id; ?></label>                           
                                <div class="col-sm-6">
                                    <input class="form-control" type="text" name="<?php echo $plugin_name; ?>_pv_id" maxlength="6" value="<?php echo $dotpay_pv_id; ?>" pattern="[0-9]{6}" title="<?php echo $text_dotpay_id_validate; ?>" style="width: auto !important;" />
                                    <?php if (isset($error['dotpay_pv_id'])) { ?>
                                        <input class="text-danger" ><?php echo $error['dotpay_pv_id']; ?></input>
                                    <?php } ?>
                                </div>                          
                            </div>
                            <div class="form-group pv-details">
                                <label class="col-sm-3 control-label"><span class="required" data-toggle="tooltip" title="<?php echo $text_dotpay_pin_help; ?>"></span> <?php echo $text_dotpay_pv_pin; ?></label>                           
                                <div class="col-sm-6">
                                    <input class="form-control" type="text" name="<?php echo $plugin_name; ?>_pv_pin" size="32" maxlength="32" value="<?php echo $dotpay_pv_pin; ?>" pattern="[0-9A-Za-z]{16,32}" title="<?php echo $text_dotpay_pin_validate; ?>" style="width: auto !important;"/>
                                    <?php if (isset($error['dotpay_pv_pin'])) { ?>
                                        <input class="text-danger"><?php echo $error['dotpay_pv_pin']; ?></input>
                                    <?php } ?>
                                </div>                          
                            </div>
                            <div class="form-group pv-details">
                                <label class="col-sm-3 control-label"><span class="required" data-toggle="tooltip" title="<?php echo $text_dotpay_pv_curr_help; ?>"></span> <?php echo $text_dotpay_pv_curr; ?></label>                           
                                <div class="col-sm-6">
                                    <input class="form-control" type="text" name="<?php echo $plugin_name; ?>_pv_curr" value="<?php echo $dotpay_pv_curr; ?>" pattern="[A-Za-z,]*" title="<?php echo $text_dotpay_pv_curr_validate; ?>" style="width: auto !important;" />
                                    <?php if (isset($error['dotpay_pv_curr'])) { ?>
                                        <input class="text-danger"><?php echo $error['dotpay_pv_curr']; ?></input>
                                    <?php } ?>
                                </div>                          
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="<?php echo $plugin_name; ?>_cc"><?php echo $text_dotpay_cc; ?></label>
                                <div class="col-sm-10">
                                    <select name="<?php echo $plugin_name; ?>_cc" id="<?php echo $plugin_name; ?>_pv" class="form-control" style="width: auto !important;">
                                        <option value="1"<?php if($dotpay_cc) { ?> selected="selected"<?php } ?>><?php echo $text_enabled; ?></option>
                                        <option value="0"<?php if(!$dotpay_cc) { ?> selected="selected"<?php } ?>><?php echo $text_disabled; ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="<?php echo $plugin_name; ?>_mp"><?php echo $text_dotpay_mp; ?></label>
                                <div class="col-sm-10">
                                    <select name="<?php echo $plugin_name; ?>_mp" id="<?php echo $plugin_name; ?>_mp" class="form-control" style="width: auto !important;">
                                        <option value="1"<?php if($dotpay_pv) { ?> selected="selected"<?php } ?>><?php echo $text_enabled; ?></option>
                                        <option value="0"<?php if(!$dotpay_pv) { ?> selected="selected"<?php } ?>><?php echo $text_disabled; ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="<?php echo $plugin_name; ?>_blik"><?php echo $text_dotpay_blik; ?></label>
                                <div class="col-sm-10">
                                    <select name="<?php echo $plugin_name; ?>_blik" id="<?php echo $plugin_name; ?>_blik" class="form-control" style="width: auto !important;">
                                        <option value="1"<?php if($dotpay_blik) { ?> selected="selected"<?php } ?>><?php echo $text_enabled; ?></option>
                                        <option value="0"<?php if(!$dotpay_blik) { ?> selected="selected"<?php } ?>><?php echo $text_disabled; ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="<?php echo $plugin_name; ?>_widget"><?php echo $text_dotpay_widget; ?></label>
                                <div class="col-sm-10">
                                    <select name="<?php echo $plugin_name; ?>_widget" id="<?php echo $plugin_name; ?>_widget" class="form-control" style="width: auto !important;">
                                        <option value="1"<?php if($dotpay_widget) { ?> selected="selected"<?php } ?>><?php echo $text_enabled; ?></option>
                                        <option value="0"<?php if(!$dotpay_widget) { ?> selected="selected"<?php } ?>><?php echo $text_disabled; ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane" id="<?php echo $plugin_name; ?>-tab-statuses">
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?=$text_dotpay_status_completed; ?></label>
                                <div class="col-sm-6">
                                    <select class="form-control" name="<?php echo $plugin_name; ?>_status_completed" style="width: auto !important;"><?php
                                        foreach ($order_statuses as $status) {
                                            echo '<option value="'.$status['order_status_id'].'"'.($status['order_status_id'] == $dotpay_status_completed ? ' selected="selected"' : '').'>'.$status['name'].'</option>';
                                        }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?=$text_dotpay_status_rejected; ?></label>
                                <div class="col-sm-6">
                                    <select class="form-control" name="<?php echo $plugin_name; ?>_status_rejected" style="width: auto !important;"><?php
                                        foreach ($order_statuses as $status) {
                                            echo'<option value="'.$status['order_status_id'].'"'.($status['order_status_id'] == $dotpay_status_rejected ? ' selected="selected"' : '').'>'.$status['name'].'</option>';
                                        }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?=$text_dotpay_status_processing; ?></label>
                                <div class="col-sm-6">
                                    <select class="form-control" name="<?php echo $plugin_name; ?>_status_processing" style="width: auto !important;"><?php
                                        foreach ($order_statuses as $status) {
                                            echo'<option value="'.$status['order_status_id'].'"'.($status['order_status_id'] == $dotpay_status_processing ? ' selected="selected"' : '').'>'.$status['name'].'</option>';
                                        }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?=$text_dotpay_status_return; ?></label>
                                <div class="col-sm-6">
                                    <select class="form-control" name="<?php echo $plugin_name; ?>_status_return" style="width: auto !important;"><?php
                                        foreach ($return_statuses as $status) {
                                            echo'<option value="'.$status['return_status_id'].'"'.($status['return_status_id'] == $dotpay_status_return ? ' selected="selected"' : '').'>'.$status['name'].'</option>';
                                        }?>
                                    </select>
                                </div>
                            </div>
                        </div>
                                                            
                        <div class="tab-pane" id="<?php echo $plugin_name; ?>-tab-cards">
                            <table id="oneclick-cards-list" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th><?php echo $ocmanage_email; ?></th>
                                        <th><?php echo $ocmanage_username; ?></th>
                                        <th><?php echo $ocmanage_card_number; ?></th>
                                        <th><?php echo $ocmanage_card_brand; ?></th>
                                        <th><?php echo $ocmanage_register_date; ?></th>
                                        <th><?php echo $ocmanage_deregister; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($cards as $card): ?>
                                    <tr>
                                        <td><?php echo $card['email']; ?></td>
                                        <td><?php echo $card['firstname']; ?>&nbsp;<?php echo $card['lastname']; ?></td>
                                        <td><?php echo $card['mask']; ?></td>
                                        <td><?php echo $card['brand']; ?></td>
                                        <td><?php echo $card['register_date']; ?></td>
                                        <td>
                                            <button data-id="<?php echo $card['cc_id']; ?>" class="card-remove" title="<?php echo $ocmanage_deregister_card; ?>">
                                                <span class="fa fa-remove"></span>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
						
                        <div class="tab-pane" id="<?php echo $plugin_name; ?>-tab-env">
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $text_dotpay_plugin_version; ?></label>
                                <div class="col-sm-3">
                                    <input class="form-control" readonly type="text" name="<?php echo $plugin_name; ?>_plugin_version" value="<?php echo $dotpay_plugin_version;?>" style="width: auto !important;" size="6" />                                                                 
                                </div>
                                <div class="col-sm-3">
                                    <?php echo $text_dotpay_plugin_version_check; ?>                                                              
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $text_dotpay_api_version; ?></label>
                                <div class="col-sm-6">
                                    <input class="form-control" readonly type="text" name="<?php echo $plugin_name; ?>_api_version" value="<?php echo $dotpay_api_version;?>" style="width: auto !important;" size="6"  />                                                                  
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $text_dotpay_URL; ?></label>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon1"><?php echo $base_url; ?></span>
                                        <input class="form-control" readonly type="text" name="<?php echo $plugin_name; ?>_URL" value="<?php echo $dotpay_URL; ?>" />    
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $text_dotpay_URLC; ?></label>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon1"><?php echo $base_url; ?></span>
                                        <input class="form-control" readonly type="text" name="<?php echo $plugin_name; ?>_URLC" value="<?php echo $dotpay_URLC; ?>"/>
                                    </div>
                                </div>
                            </div>
                        </div>
	
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function setVisibilityPVDetails() {
        if($('#<?php echo $plugin_name; ?>_pv').val()=='1') {
            $('.pv-details').show();
            $('.pv-details input').attr('required', true);
        } else {
            $('.pv-details').hide();
            $('.pv-details input').removeAttr('required')
        }
    }
    $(document).ready(function() {
        var onRemoveMessage = '<?php echo $ocmanage_on_remove_message; ?>';
        var onDoneMessage = '<?php echo $ocmanage_on_done_message; ?>';
        var onFailureMessage = '<?php echo $ocmanage_on_failure_message; ?>';
        var removeUrl = '<?php echo $ocmanage_remove_url; ?>';
        setVisibilityPVDetails();
        $('#<?php echo $plugin_name; ?>_pv').change(setVisibilityPVDetails);
        $('#oneclick-cards-list').DataTable({
            "language": {
                "url": 'view/javascript/dotpay/Dt.<?php echo $datatable_language; ?>.json'
            }
        });
        $('.card-remove').click(function(e){
            if(confirm(onRemoveMessage+' '+$(this).parents('tr').find('td:first').text()+'?')) {
                var cardId = $(this).data('id');
                $.ajax({
                    "url":removeUrl,
                    "method":"post",
                    "data":{
                        "card_id":cardId
                    }
                }).done(function(r){
                    if(r=='OK')
                        alert(onDoneMessage);
                    else {
                        console.warn(r);
                        alert(onFailureMessage);
                    }
                    location.href=location.href;
                }).fail(function(r){
                    alert(onFailureMessage);
                });
            }
            e.stopPropagation();
            return false;
        });
    });
</script>
<?php echo $footer; ?>