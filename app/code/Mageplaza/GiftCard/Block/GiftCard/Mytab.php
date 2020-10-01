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
    protected $_date;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Mageplaza\GiftCard\Model\GiftCardHistoryFactory $giftCardHistoryFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Mageplaza\GiftCard\Model\ResourceModel\GiftCardCustomerBalance\Collection $giftCardCustomerBalanceCollection,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Mageplaza\GiftCard\Helper\Data $helperData,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $date
)
    {
        parent::__construct($context);
        $this->_giftCardFactory = $giftCardFactory;
        $this->_giftCardHistoryFactory = $giftCardHistoryFactory;
        $this->_giftCardCustomerBalanceCollection = $giftCardCustomerBalanceCollection;
        $this->_customerSession = $customerSession;
        $this->_priceHelper = $priceHelper;
        $this->_helperData = $helperData;
        $this->_date = $date;
    }

    public function getHistoryCollection() {
        $customer_id = $this->_customerSession->getCustomer()->getId();
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 5;
        $collection = $this->_giftCardHistoryFactory->create()->getCollection()->setOrder('history_id', 'DESC');
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        return $collection;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getHistoryCollection()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'giftcard.history.customer'
            )->setAvailableLimit([5 => 5, 10 => 10, 15 => 15, 20 => 20])
                ->setShowPerPage(true)->setCollection(
                    $this->getHistoryCollection()
                );
            $this->setChild('pager', $pager);
            //$this->getHistoryCollection()->load();
        }
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getGCCBalance(){
        $customer_id = $this->_customerSession->getCustomer()->getId();
        $balanceCustomer = $this->_giftCardCustomerBalanceCollection->load()->addFilter('customer_id', $customer_id)->getFirstItem();
        $getCustomer = $balanceCustomer->toArray();
        if(!empty($getCustomer)) {
            $balance = $this->_priceHelper->currency($getCustomer['balance'],true,false);
        }else {
            $balance = $this->_priceHelper->currency(0,true,false);
        }
        return $balance;
    }

    public function setCurrency($curr) {
        return $this->_priceHelper->currency($curr,true,false);
    }

    public function setDate($date) {
        return date('m/d/y', strtotime($date));
        //return $this->_date->create()->date('d/m/y');
    }

    public function getGiftCardCode($giftcard_id) {
        $giftcard = $this->_giftCardFactory->create();
        $giftcard_id = $giftcard->load($giftcard_id)->getCode();
        return $giftcard_id;
    }
    public function getStatusRedeem() {
        return $this->_helperData->getGeneralConfig('allow_redeem');
    }

    public function getHistory() {
        $customer_id = $this->_customerSession->getCustomer()->getId();
        $gCHistory = $this->_giftCardHistoryFactory->create()->getCollection()
            ->addFilter('customer_id', $customer_id)
            ->join('mp_mageplaza_giftcard_code','main_table.giftcard_id=mp_mageplaza_giftcard_code.giftcard_id' ,'code')
            ->getData();
        return $gCHistory;
    }
}
