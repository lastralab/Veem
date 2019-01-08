/*browser:true*/
/*global define*/

define(
    [
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/quote',
        'ko'
    ],
    function (Component, quote) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Veem_Payment/payment/veem'
            },

            buttonType: null,

            initialize: function() {
                this._super();
                quote.billingAddress.subscribe(function (address) {
                    this.isPlaceOrderActionAllowed(address !== null && address.postcode !== null);
                }, this);
                this.buttonType = this.getButtonType();
            },

            getData: function() {
                return {
                    'method': this.item.method,
                };
            },

            isAvailable: function() {
                return window.checkoutConfig.payment[this.getCode()].active;
            },

            getButtonType: function() {
                return window.checkoutConfig.payment[this.getCode()].button;
            }
        });
    }
);