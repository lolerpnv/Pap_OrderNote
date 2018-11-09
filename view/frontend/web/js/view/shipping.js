/*global define*/
define(['jquery'], function ($) {
    'use strict';
    return function (Shipping) {
        return Shipping.extend({
            defaults: {
                template: 'Magento_Checkout/shipping'
            },
            selectShippingStoreCity: function () {
                console.log(this.rates);
                // return this.rates[0].extension_attributes.customer_notes;

                return ['ss','sss'];
            }
        });
    };
});
