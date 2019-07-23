define(
    [
        'ko',
        'underscore',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/checkout-data',
        'Magento_Customer/js/customer-data'
    ],
    function (ko, _, quote, checkoutData, customerData) {
        'use strict';

        var account = ko.observable(null);

        return {
            accountInformation: account,
            currentCustomer: customerData.get('customer'),

            initialize: function() {
                var accountInfo = {
                        email: null,
                        firstname: null,
                        lastname: null,
                        countryId: null,
                        telephone: null
                    }, _self = this;
                
                if(this.currentCustomer().firstname !== null && typeof this.currentCustomer().email === 'undefined') {
                    customerData.reload('customer');
                }

                this.initAccountInfo(accountInfo);

                quote.shippingAddress.subscribe(function(address) {
                    _self.fillInfo(accountInfo, address);
                });
            },

            initAccountInfo: function(accountInfo)
            {
                var address = quote.billingAddress();

                this.fillInfo(accountInfo, address);
            },

            fillInfo: function(accountInfo, address)
            {
                if(address !== null) {
                    accountInfo = {
                        email: typeof this.currentCustomer().email !== 'undefined' ?
                            this.currentCustomer().email :
                            checkoutData.getInputFieldEmailValue(),
                        firstname: address.firstname,
                        lastname: address.lastname,
                        countryId: address.countryId,
                        telephone: address.telephone
                    };

                    if(this.validate(accountInfo)) {
                        this.accountInformation(accountInfo);
                    }
                }
            },

            validate: function(accountInfo) {
                var isFail = false;
                if(accountInfo === null) {
                    return false;
                }
                _.each(
                    _.keys(accountInfo),
                    function(element) {
                        isFail = isFail ?
                            isFail :
                            _.isEmpty(accountInfo[element]);
                    });

                return !isFail;
            }
        };
    }
);