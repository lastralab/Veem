define(
    [
        'uiComponent',
        'Magento_Ui/js/model/messages',
        'uiLayout',
    ],
    function (Component, Messages, layout) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Veem_Payment/payment/multishipping/veem'
            },

            initialize: function() {
                this._super();
                this.initChildren();
            },

            initChildren: function() {
                this.messageContainer = new Messages();
                this.createMessagesComponent();

                return this;
            },

            createMessagesComponent: function() {
                var messagesComponent = {
                    parent: this.name,
                    name: 'messages',
                    displayArea: 'messages',
                    component: 'Magento_Ui/js/view/messages',
                    config: {
                        messageContainer: this.messageContainer
                    }
                };

                layout([messagesComponent]);

                return this;
            }
        });
    }
);