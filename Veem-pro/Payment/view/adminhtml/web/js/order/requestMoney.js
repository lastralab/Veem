define(
    [
        'jquery',
        'Magento_Ui/js/modal/alert',
        'mage/translate',
        'Magento_Ui/js/modal/modal'
    ],
    function($, alert, $t, modal) {
        'use strict';

        $.widget('veem.requestMoney', {

            _create: function() {
                var opts = {
                    title: $t('Request Money'),
                    responsive: true,
                    modalClass:'veem-request-money-modal',
                    buttons:[{
                        text: $t('Confirm Request Money'),
                        click: function() {
                            $.requestMoneyAjax()
                        },
                        class:'veem-request-money-button-confirm'
                    }]
                };

                modal(opts, $('.veem-request-money-modal-content'));
                $(".veem-button").on('click',function(){
                    $(".veem-request-money-modal-content").modal('openModal');
                });

                var form = $('#form-veem-request-money');
                $.requestMoneyAjax = function(){
                    if(form.validation().valid()) {
                        $.post({
                            showLoader: true,
                            url: form.attr('action'),
                            data: form.serialize()
                        })
                            . always(function() {
                                window.location.reload();
                        });
                    }
                }
            }
        });

        return $.veem.requestMoney;
    });