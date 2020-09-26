<?php
namespace Mageplaza\GiftCard\Controller\Index;

use \Magento\Framework\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use \Magento\Framework\App\Action\Action;
use \Magento\Framework\App\Request\Http;

class Test2 extends Action
{
    protected $_pageFactory;
    protected $_orderFactory;
    protected $orderRepository;
protected $_giftCardFactory;
    protected $_giftCardCustomerBalanceFactory;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        \Magento\Sales\Model\ResourceModel\Order\ItemFactory $orderFactory,
        \Mageplaza\GiftCard\Model\GiftCardCustomerBalanceFactory $giftCardCustomerBalanceFactory,

        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory
    )
    {
        $this->_pageFactory = $pageFactory;
        parent::__construct($context);
        $this->_orderFactory = $orderFactory;
        $this->_giftCardCustomerBalanceFactory = $giftCardCustomerBalanceFactory;

        $this->orderRepository = $orderRepository;
        $this->_giftCardFactory = $giftCardFactory;
    }

    public function execute()
    {
        $order = $this->orderRepository->get(1);
        foreach ($order->getAllItems() as $item) {
            echo $item['sku'];
        }
        $getGCHistory = $this->_giftCardFactory->create()->getCollection()->addFilter('created_from', 'admin')->setOrder('giftcard_id', 'DESC')->setPageSize(1)->toArray();
        echo "<pre>";
        print_r($getGCHistory['items']);
        echo "</pre>";
        $getGCBalance = $this->_giftCardCustomerBalanceFactory->create()->load(2, 'customer_id')->toArray();
        echo "<pre>";
        print_r($getGCBalance);
        echo "</pre>";

//        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/checkLog.log');
//        $logger = new \Zend\Log\Logger();
//        $logger->addWriter($writer);
//        $logger->info(print_r($a));
    }
}
