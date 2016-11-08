<?php echo $header; ?>
<style>.status_dotpay { font-size: 16px;}</style>
<div class="container">    
    <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
    </ul>
    <div class="row">        
        <div id="content" class="col-lg-12">
            <h1><?php echo $heading_title; ?></h1>
            <hr>
            <span class="status_dotpay"><?=$text_dotpay_info?></span><br>            
            <div class="buttons">
                <div class="pull-right"><a href="<?php echo $action_continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a></div>
            </div>            
        </div>        
    </div>
</div>
<?php echo $footer; ?>