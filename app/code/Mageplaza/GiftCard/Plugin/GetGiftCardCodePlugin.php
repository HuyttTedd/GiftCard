<?php
namespace Mageplaza\GiftCard\Plugin;

class GetGiftCardCodePlugin {
    public function afterGetCouponCode(\Magento\Checkout\Block\Cart\Coupon $subject) {
        if($subject->getQuote()->getGiftcardCode()) {
            return $subject->getQuote()->getGiftcardCode();
        } else {
            return $subject->getQuote()->getCouponCode();
        }
    }
}
