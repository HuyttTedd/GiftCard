<?php
namespace Mageplaza\GiftCard\Model\Total\Quote;

/**
 * Class Custom
 * @package Mageplaza\GiftCard\Model\Total\Quote
 */
class Custom extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;

    protected $_giftCardFactory;

    /**
     * Custom constructor.
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory
    ){
        $this->_priceCurrency = $priceCurrency;
        $this->_giftCardFactory = $giftCardFactory;
    }
    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this|bool
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    )
    {
        parent::collect($quote, $shippingAssignment, $total);
        $giftCardCode = $quote->getGiftcardCode();
        $gCInfo = $this->_giftCardFactory->create()->load($giftCardCode, 'code')->toArray();

        $subTotal = (float)$quote->getSubtotal();
        if(!empty($gCInfo)) {
            if($gCInfo['balance'] > $gCInfo['amount_used']) {
                $restBalance = (float)$gCInfo['balance'] - (float)$gCInfo['amount_used'];
                if($subTotal <= $restBalance) {
                    $giftcardBaseDiscount = $subTotal;
                } else {
                    $giftcardBaseDiscount = $restBalance;
                }

                $giftCardDiscount =  $this->_priceCurrency->convert($giftcardBaseDiscount);
                $total->addTotalAmount('customdiscount', -$giftCardDiscount);
                $total->addBaseTotalAmount('customdiscount', -$giftcardBaseDiscount);
                $quote->setGiftcardDiscount(-$giftCardDiscount)->save();

            }
        }
//        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/checkLog.log');
//        $logger = new \Zend\Log\Logger();
//        $logger->addWriter($writer);
//        $logger->info(print_r(json_decode(json_encode($quote->getData())), 1));
//        $logger->info('aaaaaaaa');
//        $logger->info($quote->getSubtotal());
//        $logger->info($quote->getGrandTotal());
        return $this;
    }


    public function fetch(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        return [
            'code' => $this->getCode(),
            'title' => $this->getLabel(),
            'value' => $quote->getGiftcardDiscount()
        ];
    }
}
