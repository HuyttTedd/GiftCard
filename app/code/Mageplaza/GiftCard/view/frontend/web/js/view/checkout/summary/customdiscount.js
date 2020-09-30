define(
    [
        'jquery',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals',
        'Magento_Catalog/js/price-utils'
    ],
    function ($,Component,quote,totals,priceUtils) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Mageplaza_GiftCard/checkout/summary/customdiscount'
    },
        totals: quote.getTotals(),
            isDisplayedCustomdiscount : function () {
            return totals.getSegment('customer_discount').value < 0;
        },
            getCustomDiscount : function () {
            var price = totals.getSegment('customer_discount').value;
            return this.getFormattedPrice(price);
        }
    });
    }
);
