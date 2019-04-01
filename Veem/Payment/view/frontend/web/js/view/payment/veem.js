define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'veem',
                component: 'Veem_Payment/js/view/payment/method-renderer/veem'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);