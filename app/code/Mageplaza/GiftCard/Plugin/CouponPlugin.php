<?php
namespace Mageplaza\GiftCard\Plugin;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

class CouponPlugin extends \Magento\Checkout\Controller\Cart {
    protected $_giftCardFactory;
    protected $quoteRepository;

    public function __construct(
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository)
    {
        $this->_giftCardFactory = $giftCardFactory;
        $this->quoteRepository = $quoteRepository;
    }
    public function execute()
    {
        // TODO: Implement execute() method.
    }

    public function aroundExecute(\Magento\Checkout\Controller\Cart\CouponPost $subject, callable $proceed) {
        $couponCode = $subject->getRequest()->getParam('remove') == 1
            ? ''
            : trim($subject->getRequest()->getParam('coupon_code'));
        $giftcard = $this->_giftCardFactory->create()->load($couponCode, 'code')->toArray();
        $codeLength = strlen($couponCode);

        $cartQuote = $subject->cart->getQuote();
        $oldGC = $cartQuote->getGiftcardCode();

        if(empty($giftcard) && !strlen($oldGC)) {
            $proceed();
            return $subject->_goBack();
        }

        if ($codeLength == 0) {
            $subject->_checkoutSession->getQuote()->setGiftcardCode($couponCode)->save();
            $subject->messageManager->addSuccessMessage(__('You canceled the gift code!'));
            return $subject->_goBack();
        } else {
            $restBalance = $giftcard['balance'] - $giftcard['amount_used'];
            if($restBalance <= 0) {
                $subject->messageManager->addErrorMessage(__('Gift Card has no money left!'));
                return $subject->_goBack();
            } else {
                $cartQuote = $subject->cart->getQuote();
                $cartQuote->getShippingAddress()->setCollectShippingRates(true);
                $cartQuote->setCouponCode($couponCode)->collectTotals();
                $this->quoteRepository->save($cartQuote);
                $subject->_checkoutSession->getQuote()->setGiftcardCode($couponCode)->save();
                $subject->messageManager->addSuccessMessage(__('Gift code applied successfully!'));
                return $subject->_goBack();
            }
        }
    }
}
