<?php echo $header; ?>

<?php echo $column_left; ?>
<style type="text/css">
    #statusMessageContainer {
        text-align: center;
    }

    #statusMessageContainer p {
        text-align: left;
    }

    /* Loader */
    .loading {
        position: relative;
        width: 100%;
        height: 70px;
    }

    .loading:after {
        font-family: Sans-Serif !important;
        box-sizing: border-box;
        content: '';
        position: absolute;
        z-index: 100;
        left: 50%;
        top: 50%;
        width: 40px;
        height: 40px;
        font-size: 40px;
        border-right: 3px solid #9e191d;
        border-bottom: 1px solid #ebebeb;
        border-top: 2px solid #9e191d;
        border-radius: 100px;
        margin: -30px 0 0 -20px; 
        animation: spin .75s infinite linear;
        -webkit-animation: spin .75s infinite linear;
        -moz-animation: spin .75s infinite linear;
        -o-animation: spin .75s infinite linear;
    }

    .spin {
        -webkit-animation: spin 1000ms infinite linear;
        animation: spin 1000ms infinite linear;
    }

    @keyframes spin {
        from { transform:rotate(0deg); }
        to { transform:rotate(360deg); }
    }

    @-webkit-keyframes spin {
        from { -webkit-transform: rotate(0deg); }
        to { -webkit-transform: rotate(360deg); }
    }
</style>
<div class="container">
    <div id="statusMessageContainer">
        <?php if($message != NULL): ?>
            <p class="alert alert-danger"><?php echo $message; ?></p>
            <script type="text/javascript">
                setTimeout(function(){location.href="<?php echo $back_redirect_url; ?>";}, 4000);
            </script>
        <?php endif; ?>
    </div>
    <div class="buttons clearfix">
        <div class="pull-left"><a href="<?php echo $back_redirect_url; ?>" class="btn btn-default"><?php echo $back_text_back; ?></a></div>
    </div>
</div>

<?php if($message == NULL): ?>
<script type="text/javascript">
    window.checkStatusConfig = {
        "url": "<?php echo $check_status_url; ?>",
        "waitingMessage": "<?php echo $back_waiting_message; ?>",
        "successMessage": "<?php echo $back_success_message; ?>",
        "errorMessage": "<?php echo $back_error_message; ?>",
        "timeoutMessage": "<?php echo $back_timeout_message; ?>",
        "redirectUrl": "<?php echo $back_redirect_url; ?>"
    };

    $(document).ready(function(){
    var timeout = 2;//in minutes
    var interval = 5;//in seconds
    var counter = 0;
    var counterLimit = timeout*60/interval;
    var lastRequest = false;
    setInfoMessage(window.checkStatusConfig.waitingMessage);
    addLoader();
    var checkInt = setInterval(function(){
        if(counter<counterLimit)
            ++counter;
        else {
            clearInterval(checkInt);
            hideLoader();
            setErrorMessage(window.checkStatusConfig.timeoutMessage);
        }
        $.get(window.checkStatusConfig.url, {}, function(e){
            switch(e) {
                case '0':
                    break;
                case '1':
                    hideLoader();
                    setSuccessMessage(window.checkStatusConfig.successMessage);
                    clearInterval(checkInt);
                    setTimeout(function(){location.href=window.checkStatusConfig.redirectUrl;}, 5000);
                    break;
                default:
                    hideLoader();
                    setErrorMessage(window.checkStatusConfig.errorMessage);
                    clearInterval(checkInt);
            }
            if(e == 'NO' || e == '-1') {
                hideLoader();
                setErrorMessage(window.checkStatusConfig.errorMessage);
            }
        });
    }, interval*1000);
});

function setMessage(message, className) {
    $('#statusMessageContainer p').remove();
    var element = document.createElement('p');
    element.className = 'alert '+className;
    element.innerHTML = message;
    $('#statusMessageContainer').append(element);
}

function setErrorMessage(message) {
    setMessage(message, 'alert-danger');
}

function setSuccessMessage(message) {
    setMessage(message, 'alert-success');
}

function setInfoMessage(message) {
    setMessage(message, 'alert-info');
}

function addLoader() {
    var element = document.createElement('div');
    element.className = 'loading';
    $('#statusMessageContainer').append(element);
}

function hideLoader() {
    $('#statusMessageContainer .loading').remove();
}

</script>
<?php endif; ?>

<?php echo $footer; ?>