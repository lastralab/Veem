define(
    [
        'jquery',
        'underscore',
        'Magento_Ui/js/modal/alert',
        'mage/translate',
        'Veem_Payment/js/model/account'
    ],
    function($, _, alert, $t, veemAccount) {
        'use strict';

        return function(paymentWidget) {
            $.widget(
                'mage.payment',
                $['mage']['payment'],
                {
                    _validatePaymentMethod: function () {
                        var methods = this.element.find('[name^="payment["]'),
                            isValid = false;

                        if (methods.length === 0) {
                            alert({
                                content: $t('We can\'t complete your order because you don\'t have a payment method set up.')
                            });
                        } else if (this.options.checkoutPrice <= this.options.minBalance) {
                            isValid = true;
                        } else if (methods.filter('input:radio:checked').length) {
                            isValid = true;
                        } else {
                            alert({
                                content: $t('Please choose a payment method.')
                            });
                        }

                        var currentMethod = this._getSelectedPaymentMethod();

                        if(currentMethod.val() === 'veem') {
                            var accountInfo = veemAccount.accountInformation();

                            isValid = veemAccount.validate(accountInfo);

                            if(!isValid) {
                                alert({
                                    content: $t('Please fill veem account information and save it.')
                                });
                            }
                        }

                        return isValid;
                    },

                    _submitHandler: function (e) {
                        var currentMethod,
                            submitButton;

                        e.preventDefault();

                        if (this._validatePaymentMethod()) {
                            currentMethod = this._getSelectedPaymentMethod();

                            if(currentMethod.val() === 'veem') {
                                var accountInfo = veemAccount.accountInformation(),
                                    form = $('#multishipping-billing-form');
                                _.each(
                                    _.keys(accountInfo),
                                    function(element) {
                                        var ogElement = element;
                                        switch(element) {
                                            case 'firstname':
                                                element = 'fname';
                                                break;
                                            case 'lastname':
                                                element = 'lname';
                                                break;
                                            case 'telephone':
                                                element = 'phone';
                                        }

                                        form.append('<input id="payment-' + ogElement + '" type="hidden" name="payment[additional_data][' + element + ']" value="' + accountInfo[ogElement] + '">');
                                    }
                                );
                            }

                            submitButton = currentMethod.parent().next('dd').find('button[type=submit]');

                            if (submitButton.length) {
                                submitButton.first().trigger('click');
                            } else {
                                this.element.submit();
                            }
                        }
                    }
                }
            );
        };
    }
);