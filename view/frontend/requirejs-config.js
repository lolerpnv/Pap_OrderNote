var config = {
    config: {
        mixins: {
            //pass order note on place order
            'Magento_Checkout/js/action/place-order': {
                'Pap_OrderNote/js/model/place-order-note-mixin': true
            },
            'Magento_Checkout/js/view/shipping': {
                'Pap_OrderNote/js/view/shipping': true
            }
        }
    },
    map: {
        '*': {
            'Magento_Checkout/template/shipping.html':
                'Pap_OrderNote/template/shipping.html'
        }
    }
};