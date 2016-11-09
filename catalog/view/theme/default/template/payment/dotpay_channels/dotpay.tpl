<?php if($widget_visible): ?>
<link href="<?php echo $dotpay_url; ?>widget/payment_widget.min.css" rel="stylesheet">
<style>
    .selected-channel-message {
        display: none;
        font-size: 17px;
        text-decoration: none;
        margin-bottom: 5px;
    }

    .selected-channel-message a {
        text-decoration: none;
    }

    .selectedChannelContainer {
        max-width: none !important;
        cursor: default;
        display: none;
        margin-bottom: 15px !important;
    }

    .selectedChannelContainer hr {
        display: block;
    }
</style>
<script type="text/javascript">
    var dotpayWidgetConfig = {
        sellerAccountId: <?php echo $widget["id"]; ?>,
        amount: <?php echo $widget["amount"]; ?>,
        currency: '<?php echo $widget["currency"]; ?>',
        lang: '<?php echo $widget["lang"]; ?>',
        widgetFormContainerClass: 'my-form-widget-container',
        offlineChannel: 'mark',
        offlineChannelTooltip: true,
        disabledChannels: [<?php echo $widget["disabled_channels"]; ?>],
        host: '<?php echo $dotpay_url; ?>payment_api/channels/'
    };
    
    (function($) {
        var defaults = {
            channelsContainerClass: "dotpay-channels-selection",
            channelChangeClass: "channel-selected-change",
            selectedChannelContainerClass: "selectedChannelContainer",
            messageContainerClass: "selected-channel-message",
            collapsibleWidgetTitleClass: "collapsibleWidgetTitle",
            widgetContainerClass: "my-form-widget-container"
        };

        var settings = {};

        $.dpCollapsibleWidget = function(options) {
            if(window.dotpayRegisterWidgetEvent == undefined) {
                window.dotpayRegisterWidgetEvent = true;
                settings = $.extend( {}, defaults, options );
                connectEventToWidget();
                $('.'+settings.selectedChannelContainerClass+', .'+settings.messageContainerClass).click(function(e){
                    e.stopPropagation();
                    e.preventDefault();
                    return false;
                });
                $('.'+settings.channelChangeClass).click(onChangeSelectedChannel);
            }
            return this;
        }
        function connectEventToWidget() {
            $('.channel-container').on('click', function(e) {
                $('.channel-input', this).prop('checked', true);
                var id = $(this).find('.channel-input').val();
                if(id == undefined)
                    return false;
                var container = copyChannelContainer(id);
                $('.'+settings.selectedChannelContainerClass+' div').remove();
                container.insertBefore($('.'+settings.selectedChannelContainerClass+' hr'));
                toggleWidgetView();
                e.preventDefault();
            });
        }

        function copyChannelContainer(id) {
            var container = $('.'+settings.widgetContainerClass+' #'+id).parents('.channel-container').clone();
            container.find('.tooltip').remove();
            container.find('.input-container').remove();
            container.removeClass('not-online');
            return container;
        }

        function onChangeSelectedChannel(e) {
            toggleWidgetView();
            e.stopPropagation();
            e.preventDefault();
            return false;
        }

        function toggleWidgetView() {
            $('.'+settings.collapsibleWidgetTitleClass+', .'+settings.selectedChannelContainerClass+' hr, .'+settings.widgetContainerClass).animate(
                {
                    height: "toggle",
                    opacity: "toggle"
                }, {
                    duration: "slow"
                }
            );
            $('.'+settings.messageContainerClass+',.'+settings.selectedChannelContainerClass).show();
        }
    })(jQuery);
    $(document).ready(function(){
        setTimeout(function(){
            $.dpCollapsibleWidget();
        }, 1000);
    });
</script>
<script id="dotpay-payment-script" src="<?php echo HTTPS_SERVER; ?>catalog/view/javascript/dotpay/payment_widget.js"></script>
<div class="selected-channel-message"><?php echo $label_selected_channel; ?>: <a href="#" class="channel-selected-change"><?php echo $label_change_channel; ?>&nbsp;&raquo;</a></div>
<div class="selectedChannelContainer channels-wrapper"><hr /></div>
<div class="collapsibleWidgetTitle"><?php echo $label_available_channels; ?>:</div><p class="my-form-widget-container"></p>
<?php endif; ?>