<?php
namespace Mageplaza\GiftCard\Plugin;

class GetGiftCardCodePlugin {
    public function afterGetCouponCode(\Magento\Checkout\Block\Cart\Coupon $subject) {
        return $subject->getQuote()->getGiftcardCode();
    }
}
