define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list'
], function (Component, rendererList) {
    'use strict';

    rendererList.push({
        type: 'pronko_authorizenetvisa',
        component: 'Pronko_AuthorizenetVisa/js/view/payment/method-renderer/visa-checkout'
    });

    return Component.extend({});
});
