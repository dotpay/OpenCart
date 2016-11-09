<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title><?php echo $title; ?></title>
        <script type="text/javascript">
            setTimeout(function(){document.getElementById('dotpay-form').submit();}, 1);
        </script>
    </head>
    <body>
        <form action="<?=$action; ?>" method="POST" id="dotpay-form" style="display: none">  
            <?php foreach($fields as $name => $field): ?>
            <input type="hidden" name="<?php echo $name; ?>" value="<?php echo $field; ?>" />
            <?php endforeach; ?>
        </form>
    </body>
</html>