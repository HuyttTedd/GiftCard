<?php
namespace Mageplaza\GiftCard\Plugin;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

class CouponPlugin extends \Magento\Checkout\Controller\Cart {
    protected $_giftCardFactory;
    protected $quoteRepository;
    protected $_helperData;

    public function __construct(
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Mageplaza\GiftCard\Helper\Data $helperData,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository)
    {
        $this->_giftCardFactory = $giftCardFactory;
        $this->quoteRepository = $quoteRepository;
        $this->_helperData = $helperData;
    }
    public function execute()
    {
        // TODO: Implement execute() method.
    }

    public function aroundExecute(\Magento\Checkout\Controller\Cart\CouponPost $subject, callable $proceed) {
        $couponCode = $subject->getRequest()->getParam('remove') == 1
            ? ''
            : trim($subject->getRequest()->getParam('coupon_code'));
        $allowApplyGiftCard = $this->_helperData->getGeneralConfig('allow_checkout');
        $giftcard = $this->_giftCardFactory->create()->load($couponCode, 'code')->toArray();
        $codeLength = strlen($couponCode);

        $cartQuote = $subject->cart->getQuote();
        $oldGC = $cartQuote->getGiftcardCode();

        if(empty($giftcard) && !strlen($oldGC)) {
            $proceed();
            return $subject->_goBack();
        }
        $itemsCount = $cartQuote->getItemsCount();

        if ($codeLength == 0) {
            $subject->_checkoutSession->getQuote()->setGiftcardCode($couponCode)->save();
            $subject->_checkoutSession->getQuote()->setGiftcardDiscount(0)->save();
            $subject->messageManager->addSuccessMessage(__('You canceled the gift code!'));
            return $subject->_goBack();
        } else {
            if($allowApplyGiftCard == 1) {
                if ($itemsCount) {
                    $cartQuote->getShippingAddress()->setCollectShippingRates(true);
                    $cartQuote->setGiftcardCode($couponCode)->collectTotals();
                    $this->quoteRepository->save($cartQuote);
                }

                $restBalance = $giftcard['balance'] - $giftcard['amount_used'];
                if ($restBalance <= 0) {
                    $subject->messageManager->addErrorMessage(__('Gift Card has no money left!'));
                    return $subject->_goBack();
                } else {
                    $subject->_checkoutSession->getQuote()->setGiftcardCode($couponCode)->save();
                    $subject->messageManager->addSuccessMessage(__('Gift code applied successfully!'));
                    return $subject->_goBack();
                }
            } else {
                $proceed();
                return $subject->_goBack();
            }
        }
    }
}
