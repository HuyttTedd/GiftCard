<?php
namespace Mageplaza\GiftCard\Observer;

use Magento\TestFramework\Inspection\Exception;

class PalaceOrder implements \Magento\Framework\Event\ObserverInterface
{
    protected $_giftCardHistoryFactory;
    protected $_giftCardCustomerBalanceFactory;
    protected $_giftCardFactory;
    protected $_resourceConnection;
    protected $_helperData;
    protected $_mathRandom;
    protected $_product;
    protected $_customerSession;
    protected $_conn;
    protected $orderRepository;
    protected $_messageManager;

    public function __construct(
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Mageplaza\GiftCard\Model\GiftCardHistoryFactory $giftCardHistoryFactory,
        \Mageplaza\GiftCard\Model\GiftCardCustomerBalanceFactory $giftCardCustomerBalanceFactory,
        \Mageplaza\GiftCard\Helper\Data $helperData,
        \Magento\Framework\Math\Random $mathRandom,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    )
    {
        $this->_giftCardFactory = $giftCardFactory;
        $this->_giftCardHistoryFactory = $giftCardHistoryFactory;
        $this->_giftCardCustomerBalanceFactory = $giftCardCustomerBalanceFactory;
        $this->_helperData = $helperData;
        $this->_mathRandom = $mathRandom;
        $this->_product = $product;
        $this->_customerSession = $customerSession;
        $this->_resourceConnection = $resourceConnection;
        $this->orderRepository = $orderRepository;
        $this->_messageManager = $messageManager;
    }


    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getData('order');
        $order_id = $order->getEntityId();
        $customer_id = $this->_customerSession->getCustomer()->getId();

        $order = $this->orderRepository->get($order_id);

        $this->_conn = $this->_resourceConnection->getConnection();
        foreach ($order->getAllItems() as $value) {
            if($value['sku'] == 'Gift Card' && $value['is_virtual'] == 1) {
                $chars = 'ABCDEFGHIJKLMLOPQRSTUVXYZ0123456789';
                $qtyGiftCard = $value['qty_ordered'];
                $product_id = $value['product_id'];
                $codelength = $this->_helperData->getCodeConfig('code_length');
                $product = $this->_product->create()->load($product_id);
                $myattribute = $product->getResource()->getAttribute('giftcard_amount')->getFrontend()->getValue($product);
                $order_increment_id = $order->getIncrementId();
                try {
                    $this->_conn->beginTransaction();

                for($i = 0; $i < $qtyGiftCard; $i++) {
                    $j = 0;

                    while($j < 10) {
                        $code = $this->_mathRandom->getRandomString($codelength, $chars);
                        $checkRepeatCode = $this->_giftCardFactory->create()->load($code, 'code')->toArray();
                        if(empty($checkRepeatCode)) {
                            //Insert into table Gift Card
                            $newGiftCard = $this->_giftCardFactory->create();
                            $dataNewGC = [
                                'code' => $code,
                                'balance' => $myattribute,
                                'amount_used' => 0,
                                'created_from' => $order_increment_id
                            ];
                            $newGiftCard->addData($dataNewGC)->save();
                            //Insert into table Gift Card History
                            $getGCHistory = $this->_giftCardFactory->create()
                                ->getCollection()->addFilter('created_from', $order_increment_id)
                                ->setOrder('giftcard_id', 'DESC')->setPageSize(1)->toArray();
                            $giftcard_id = $getGCHistory['items'][0]['giftcard_id'];
                            $gCHistory = $this->_giftCardHistoryFactory->create();

                            $dataHistory = [
                                'giftcard_id' => $giftcard_id,
                                'customer_id' => $customer_id,
                                'amount'      => $myattribute,
                                'action'      => 'create from #'.$order_increment_id
                            ];
                            $gCHistory->addData($dataHistory)->save();
                            $j = 11;
                        } else {
                            if($j == 9) {
                                $this->_messageManager->addErrorMessage('Something went wrong. Please try again!');
                                return $this;
                            } else {
                                $j++;
                            }
                        }
                    }

                    }
                    //Insert into table Gift Card Customer Balance
                    $getGCBalance = $this->_giftCardCustomerBalanceFactory->create()->load($customer_id, 'customer_id')->toArray();

                    if(empty($getGCBalance)) {
                        $gCCBalance = $this->_giftCardCustomerBalanceFactory->create();
                        $dataGCCBalance = [
                            'customer_id' => $customer_id,
                            'balance'     => 0
                        ];
                        $gCCBalance->addData($dataGCCBalance)->save();
                    }

                    $this->_conn->commit();
                }
                catch (Exception $e) {
                    $this->_messageManager->addErrorMessage('Something went wrong!');
                }
            }
        }

        return $this;
    }
}
