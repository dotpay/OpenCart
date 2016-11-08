<?php echo $header; ?>

<?php echo $column_left; ?>

<div class="container">
    <p class="alert alert-success"><?php echo $ocmanage_alert_info; ?></p>
    <?php if($cards_exists): ?>
    <table id="credit-cards-list" class="col-sm-12 table table-striped table-bordered table-hover">
        <tr class="ocheader">
            <th><?php echo $ocmanage_card_number; ?></th>
            <th><?php echo $ocmanage_card_brand; ?></th>
            <th><?php echo $ocmanage_register_date; ?></th>
            <th><?php echo $ocmanage_deregister; ?></th>
        </tr>
        <?php foreach($cards as $card): ?>
        <tr> 
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
    </table>
    <?php else: ?>
    <p class="alert alert-danger">
        <?php echo $ocmanage_alert_notfound; ?>
        <button type="button" class="close" data-dismiss="alert">×</button>
    </p>
    <?php endif; ?>
</div>
<script>
    var onRemoveMessage = '<?php echo $ocmanage_on_remove_message; ?>';
    var onDoneMessage = '<?php echo $ocmanage_on_done_message; ?>';
    var onFailureMessage = '<?php echo $ocmanage_on_failure_message; ?>';
    var removeUrl = '<?php echo $ocmanage_remove_url; ?>';
    $(document).ready(function(){
        $('.card-remove').click(function(){
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
        });
    });
</script>
<?php echo $footer; ?>