<?php echo $header; ?>

<script type="text/javascript">
   
    $(document).ready(function () {
        
        $('.btn-toggle').click(function() {
            
            $(this).find('.btn').toggleClass('active');  

            if ($(this).find('.btn-primary').size()>0) {
                $(this).find('.btn').toggleClass('btn-primary');
            }
            if ($(this).find('.btn-default').size()>0) {
                $('input[name=dotpay_request_url]').val($(this).find('.btn-default').data('url'));                
            }
            $(this).find('.btn').toggleClass('btn-default');

        });
    });
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
            <?php if (isset($error['permission'])) { ?>
            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?=$error['permission'] ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            <?php } ?>
            <div class="panel panel-default" width="360%">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i><?php echo $text_edit; ?></h3>
                </div>
				<div class="panel-heading">
					<p align="center"><br/><a href="http://dotpay.pl/" target="_blank" /><img align="Dotpay" style="border: 0;" title="Dotpay" src="../image/dotpay/dotpay.png" /></a></p>
				<?php if ((!isset($dotpay_id)) || ($dotpay_request_url != $dotpay_production_url )) { ?>
						<p align="center"><a href="https://ssl.dotpay.pl/s2/login/registration/?affilate_id=opencart" target="_blank" class="btn btn-primary btn-lg" title="<?=$text_dotpay_register; ?>"><i class="fa fa-plus"></i><b> <?=$text_dotpay_register; ?>  </b></a></p><br/>
				<?php } ?>		
				</div>
					



                <form action="<?=$action; ?>" method="post" enctype="multipart/form-data" id="form">
                    <div style="margin: 17px 18px 27px 47px; font-size: 15px; " class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?php if($dotpay_status != '1'){echo "<font color='red'>";}else{echo "<font color='green'>";}; ?><?=$text_active_status; ?></font></label>
                            <div class="col-sm-6">
                                <select class="form-control" name="dotpay_status">
                                    <option value="1"><?=$text_enabled; ?></option>
                                    <option value="0"<?=(!$dotpay_status ? ' selected="selected"' : '' ); ?>><?=$text_disabled; ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?=$text_sort_order; ?></label>
                            <div class="col-sm-6"><input class="form-control" type="number" name="dotpay_sort_order" value="<?php if($dotpay_sort_order ==''){echo '1';}else{ echo $dotpay_sort_order;}; ?>"/></div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span class="required" data-toggle="tooltip" title="<?=$text_dotpay_id_help; ?>"></span> <?=$text_dotpay_id; ?></label>                           
                            <div class="col-sm-6">
                                <input class="form-control" type="text" name="dotpay_id" maxlength="6" value="<?=$dotpay_id; ?>" pattern="[0-9]{6}" title="<?=$text_dotpay_id_validate; ?>" />
                                <?php if (isset($error['dotpay_id'])) { ?>
                                    <small class="text-danger"><?=$error['dotpay_id']; ?></small>
                                <?php } ?>
                            </div>                          
                        </div>  
                        <div class="form-group">
                            <label class="col-sm-3 control-label" ><span class="required" data-toggle="tooltip" title="<?=$text_dotpay_pin_help; ?>"></span> <?=$text_dotpay_pin; ?></label>
                            <div class="col-sm-6" >
                                <input  class="form-control" type="text" size="32" maxlength="32" name="dotpay_pin" value="<?=$dotpay_pin; ?>" pattern="[0-9A-Za-z]{16,32}"  title="<?=$text_dotpay_pin_validate; ?>" /> 
                                <?php if (isset($error['dotpay_pin'])) { ?>
                                    <small class="text-danger"><?=$error['dotpay_pin']; ?></small>
                                <?php } ?>
                            </div>                            
                        </div>
					    <div class="form-group form-inline">
                            <label class="col-sm-3 control-label"><span class="required" data-toggle="tooltip" title="<?=$text_dotpay_proddev_help; ?>"></span> <?=$text_dotpay_switch_version; ?><?php if ($dotpay_request_url != $dotpay_production_url ) echo "<div style='color:orange; font-size: 0.8em;'>".$text_dotpay_proddev_help_2."</div>"; ?></label>
                            <div class="col-sm-6">
                                <div class="btn-group btn-toggle"> 
                                    <?php if ($dotpay_request_url != $dotpay_production_url ) { ?>
										<button type="button" class="btn btn-default" data-url="<?=$dotpay_production_url; ?>"><?=$text_dotpay_production?></button>
                                        <button type="button" class="btn btn-primary active" data-url="<?=$dotpay_development_url;?>"><?=$text_dotpay_development?></button>
                                               
                                    <?php }else { ?>
										<button type="button" class="btn btn-primary active" data-url="<?=$dotpay_production_url; ?>"><?=$text_dotpay_production?></button> 
                                        <button type="button" class="btn btn-default" data-url="<?=$dotpay_development_url; ?>"><?=$text_dotpay_development?></button> 
                                    <?php } ?>                                    
                                </div> 
                                <input class="form-control" style="width:240px" readonly type="text" name="dotpay_request_url" value="<?=$dotpay_request_url; ?>" />                                    
                            </div>                           
                        </div> 

						<div class="form-group">
                            <label class="col-sm-3 control-label"><?=$text_dotpay_status_completed; ?><?php if($dotpay_status_completed !='5'){echo "<div style='color:orange; font-size: 0.8em;'>".$text_dotpay_status_completed_2."</div>";} ?></label>
                            <div class="col-sm-6">
                                <select class="form-control" name="dotpay_status_completed"><?php
                                    foreach ($order_statuses as $status) {
                                    echo '<option value="'.$status['order_status_id'].'"'.($status['order_status_id'] == $dotpay_status_completed ? ' selected="selected"' : '').'>'.$status['name'].'</option>';
                                    }?>
                                </select>
                            </div>
                        </div>	
						
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?=$text_dotpay_status_rejected; ?><?php if($dotpay_status_rejected !='7'){echo "<div style='color:orange; font-size: 0.8em;'>".$text_dotpay_status_rejected_2."</div>";} ?></label>
                            <div class="col-sm-6">
                                <select class="form-control" name="dotpay_status_rejected"><?php
                                    foreach ($order_statuses as $status) {
                                    echo'<option value="'.$status['order_status_id'].'"'.($status['order_status_id'] == $dotpay_status_rejected ? ' selected="selected"' : '').'>'.$status['name'].'</option>';
                                    }?>
                                </select>
                            </div>
                        </div>                        

                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?=$text_dotpay_status_processing; ?><?php if($dotpay_status_processing !='2'){echo "<div style='color:orange; font-size: 0.8em;'>".$text_dotpay_status_processing_2."</div>";} ?></label>
                            <div class="col-sm-6">
                                <select class="form-control" name="dotpay_status_processing"><?php
                                    foreach ($order_statuses as $status) {
                                    echo'<option value="'.$status['order_status_id'].'"'.($status['order_status_id'] == $dotpay_status_processing ? ' selected="selected"' : '').'>'.$status['name'].'</option>';
                                    }?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?=$text_dotpay_return_status_completed; ?></label>
                            <div class="col-sm-6">
                                <select class="form-control" name="dotpay_return_status_completed"><?php
                                    foreach ($return_statuses as $status) {
                                    echo'<option value="'.$status['return_status_id'].'"'.($status['return_status_id'] == $dotpay_return_status_completed ? ' selected="selected"' : '').'>'.$status['name'].'</option>';
                                    }?>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?=$text_dotpay_ip; ?></label>
                            <div class="col-sm-6">
                                <input class="form-control" readonly type="text" name="dotpay_ip" value="<?=$dotpay_ip;?>" size="16" maxlength="16" />                                                                  
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?=$text_dotpay_URL; ?></label>
                            <div class="col-sm-6">         
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1"><?=HTTPS_SERVER; ?></span>
                                    <input class="form-control" readonly type="text" name="dotpay_URL" value="<?=$dotpay_URL; ?>" />    
                                </div>
                            </div>                            
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?=$text_dotpay_URLC; ?></label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1"><?=HTTPS_SERVER; ?></span>
                                    <input class="form-control" readonly type="text" name="dotpay_URLC" value="<?=$dotpay_URLC; ?>"/>
                                </div>
                            </div>
                        </div>            
         
                    </div>          
                    <input type="hidden" name="dotpay_development_url" value="<?=$dotpay_development_url; ?>" />
                    <input type="hidden" name="dotpay_production_url" value="<?=$dotpay_production_url; ?>" />
                    <input type="hidden" name="dotpay_request_method" value="<?=$dotpay_request_method; ?>" />
                    <input type="hidden" name="dotpay_api_version" value="<?=$dotpay_api_version; ?>" />                    
                    <input type="hidden" name="dotpay_type" value="<?=$dotpay_type; ?>" />
                </form>
            </div>            
        </div>
    </div>
</div>



<?=$footer; ?>