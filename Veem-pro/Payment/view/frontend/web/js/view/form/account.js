define(
    [
        'ko',
        'Magento_Ui/js/form/form',
        'Magento_Customer/js/customer-data',
        'Veem_Payment/js/model/account'
    ],
    function(ko, Component, customerData, VeemAccount) {
        'use strict';

        return Component.extend(
            {
                defaults : {
                    template: 'Veem_Payment/form/account',
                    links: {
                        isPlaceOrderActionAllowed: 'checkout.steps.billing-step.payment.payments-list.veem:isPlaceOrderActionAllowed'
                    }
                },
                isPlaceOrderActionAllowed: ko.observable(false),
                isAccountDetailsVisible: ko.observable(false),
                currentAccountInformation: VeemAccount.accountInformation,
                countryData: customerData.get('directory-data'),

                initialize: function () {
                    this._super();
                    var _self = this;
                    VeemAccount.accountInformation.subscribe(function(accountInfo) {
                        _self.isAccountDetailsVisible(VeemAccount.validate(accountInfo));
                    });
                },

                updateInfo: function (data, event) {
                    event.preventDefault();
                    this.source.set('params.invalid', false);
                    this.source.trigger(this.dataScopePrefix + '.data.validate');

                    // verify that form data is valid
                    if (!this.source.get('params.invalid')) {
                        // data is retrieved from data provider by value of the customScope property
                        var formData = this.source.get(this.dataScopePrefix);
                        VeemAccount.accountInformation(formData);
                    }
                },

                getCountryName: function(countryId) {
                    return typeof this.countryData()[countryId] !== 'undefined' ? this.countryData()[countryId].name : '';
                },

                editAccount: function(data, event) {
                    event.preventDefault();
                    this.isAccountDetailsVisible(false);
                    this.isPlaceOrderActionAllowed(false);
                },

                cancelAccount: function(data, event) {
                    if(VeemAccount.validate(this.currentAccountInformation())) {
                        this.isAccountDetailsVisible(true);
                        this.isPlaceOrderActionAllowed(true);
                    }

                    this.reset();
                }
            }
        );
    }
);