define(
    [
        'jquery',
        'Magento_Ui/js/modal/alert',
        'mage/translate'
    ],
    function($, alert, $t) {
        'use strict';

        $.widget('veem.tokenGenerator', {
            options: {
                url: ''
            },

            _create: function() {
                this._super();
                this._bind();
            },

            _bind: function() {
                $(this.element).click(this.generateToken.bind(this));
            },

            generateToken: function(event) {
                event.preventDefault();

                $.get({
                    showLoader: true,
                    url : this.options.url,
                    data: {
                        website: $('#website_switcher').val(),
                        store: $('#store_switcher').val()
                    }
                })
                    .done(function(data) {
                        alert({
                            title: $t('Success'),
                            content: $t(data.msg),
                            autoOpen: true
                        });
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        console.error(errorThrown);
                        alert({
                            title: $t('Error!'),
                            content: $t('An error occurred while generating the token. Please try again later.'),
                            autoOpen: true
                        });
                    });
            }
        });

        return $.veem.tokenGenerator;
});