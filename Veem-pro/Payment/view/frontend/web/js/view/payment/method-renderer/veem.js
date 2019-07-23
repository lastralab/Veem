define(
    [
        'Magento_Checkout/js/view/payment/default',
        'Veem_Payment/js/model/account'
    ],
    function (Component, veemAccount) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Veem_Payment/payment/veem'
            },

            buttonType: null,

            initialize: function() {
                var _self = this;
                this._super();

                veemAccount.accountInformation.subscribe(function (accountInfo) {
                    _self.isPlaceOrderActionAllowed(veemAccount.validate(accountInfo));
                }, this);
                this.buttonType = this.getButtonType();

                veemAccount.initialize();
            },

            getData: function() {
                var accountInfo = veemAccount.accountInformation();

                if(accountInfo === null) {
                    return this._super();
                }

                return {
                    'method': this.item.method,
                    'additional_data': {
                        'email': accountInfo.email,
                        'fname': accountInfo.firstname,
                        'lname': accountInfo.lastname,
                        'country': accountInfo.countryId,
                        'phone': accountInfo.telephone
                    }
                };
            },

            isAvailable: function () {
                return window.checkoutConfig.payment[this.getCode()].active;
            },

            getButtonType: function() {
                return window.checkoutConfig.payment[this.getCode()].button;
            }

        });
    }
);