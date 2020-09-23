<?php
namespace Mageplaza\GiftCard\Block\GiftCard;

class Mytab extends \Magento\Framework\View\Element\Template
{
    protected $_giftCardHistoryFactory;
    protected $_giftCardCustomerBalanceCollection;
    protected $_giftCardFactory;
    protected $_customerSession;
    protected $_priceHelper;
    protected $_helperData;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Mageplaza\GiftCard\Model\GiftCardHistoryFactory $giftCardHistoryFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Mageplaza\GiftCard\Model\ResourceModel\GiftCardCustomerBalance\Collection $giftCardCustomerBalanceCollection,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Mageplaza\GiftCard\Helper\Data $helperData
)
    {
        parent::__construct($context);
        $this->_giftCardFactory = $giftCardFactory;
        $this->_giftCardHistoryFactory = $giftCardHistoryFactory;
        $this->_giftCardCustomerBalanceCollection = $giftCardCustomerBalanceCollection;
        $this->_customerSession = $customerSession;
        $this->_priceHelper = $priceHelper;
        $this->_helperData = $helperData;
    }


    public function getGCCBalance(){
        $customer_id = $this->_customerSession->getCustomer()->getId();
        $balanceCustomer = $this->_giftCardCustomerBalanceCollection->load()->addFilter('customer_id', $customer_id)->getFirstItem();
        $getCustomer = $balanceCustomer->toArray();
        $balance = $this->_priceHelper->currency($getCustomer['balance'],true,false);
        return $balance;
    }

    public function getStatusRedeem() {
        return $this->_helperData->getGeneralConfig('allow_redeem');
    }
}
