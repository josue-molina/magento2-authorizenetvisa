define([
    'mage/storage',
    'Magento_Checkout/js/model/url-builder',
    'Magento_Checkout/js/action/redirect-on-success',
    'Magento_Customer/js/model/customer'
], function (storage, urlBuilder, redirectOnSuccessAction, customer) {
    'use strict';

    return {
        execute: function (response, messageContainer) {
            var serviceUrl,
                payload = {
                    'response': JSON.stringify(response)
                };

            if (!customer.isLoggedIn()) {
                serviceUrl = urlBuilder.createUrl('/guest-carts/mine/pronko-set-visa-checkout-payment-information', {});
            } else {
                serviceUrl = urlBuilder.createUrl('/carts/mine/pronko-set-visa-checkout-payment-information', {});
            }
            storage.post(
                serviceUrl, JSON.stringify(payload)
            ).done(function () {
                redirectOnSuccessAction.execute();
            }).fail(function (response) {
                var message;
                if (response && response.responseJSON) {
                    message = response.responseJSON;
                } else {
                    message = 'Something went wrong. Please contact the administrator!';
                }
                messageContainer.addErrorMessage({
                    message: message
                });
            }).always(function () {
            });
        }
    };
});
