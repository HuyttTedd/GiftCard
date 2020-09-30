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
    public $aa = 0;
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

        $tax = $quote->getTotals()['tax']->toArray()['value'];
        $subTotal = $quote->getTotals()['subtotal']->toArray()['value'];
        $subTotalAndTax = (float)$tax + (float)$subTotal;
        if(!empty($gCInfo)) {
            if($gCInfo['balance'] > $gCInfo['amount_used']) {
                $restBalance = (float)$gCInfo['balance'] - (float)$gCInfo['amount_used'];
                if($subTotalAndTax <= $restBalance) {
                    $baseDiscount = $subTotalAndTax;
                } else {
                    $baseDiscount = $restBalance;
                }
                $discount =  $this->_priceCurrency->convert($baseDiscount);
                $total->addTotalAmount('customdiscount', -$discount);
                $total->addBaseTotalAmount('customdiscount', -$baseDiscount);
                $total->setBaseGrandTotal($total->getBaseGrandTotal() - $baseDiscount);
                $quote->setCustomDiscount(-$discount);
                $this->aa = $discount;
            }
        }
        return $this;
    }

    public function fetch(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        return [
            'code' => $this->getCode(),
            'title' => $this->getLabel(),
            'value' => -$this->aa
        ];
    }
}
