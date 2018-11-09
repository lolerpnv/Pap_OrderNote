/*jshint browser:true jquery:true*/
/*global alert*/
define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';
    return function (placeOrderAction) {
        /** Override default place order action and add agreement_ids to request */
        return wrapper.wrap(placeOrderAction, function(originalAction, paymentData, redirectOnSuccess, messageContainer) {
            var order_note = jQuery('#order-note').val();
            paymentData.additional_data = { order_note:order_note };

            return originalAction(paymentData, redirectOnSuccess, messageContainer);
        });
    };
});