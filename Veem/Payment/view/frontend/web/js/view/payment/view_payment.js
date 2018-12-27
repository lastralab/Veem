
/*browser:true*/
/*global define*/

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
                type: 'veem_payment',
                component: 'Veem_Payment/js/view/payment/method-renderer/view_payment'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);