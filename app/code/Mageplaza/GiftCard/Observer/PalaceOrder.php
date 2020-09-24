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

    public function __construct(
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Mageplaza\GiftCard\Model\GiftCardHistoryFactory $giftCardHistoryFactory,
        \Mageplaza\GiftCard\Model\GiftCardCustomerBalanceFactory $giftCardCustomerBalanceFactory,
        \Mageplaza\GiftCard\Helper\Data $helperData,
        \Magento\Framework\Math\Random $mathRandom,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\ResourceConnection $resourceConnection
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
    }


    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getData('order');
        $order_id = $order->getEntityId();
        $customer_id = $this->_customerSession->getCustomer()->getId();

        $connection = $this->_resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $tablename = $connection->getTableName('mp_sales_order_item');
        $query = "select sku, is_virtual, qty_ordered, product_id from ".$tablename." where order_id = '$order_id'";
        $getToCheck = $connection->fetchAll($query);

//        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/checkLog.log');
//        $logger = new \Zend\Log\Logger();
//        $logger->addWriter($writer);
//        $logger->info(print_r($getToCheck));


        $this->_conn = $this->_resourceConnection->getConnection();
        foreach ($getToCheck as $key => $value) {
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
                    $code = $this->_mathRandom->getRandomString($codelength, $chars);

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
                        $tablename = $connection->getTableName('mp_mageplaza_giftcard_code');
                        $query = "select giftcard_id from ".$tablename."
                        where created_from ='$order_increment_id' order by giftcard_id DESC limit 1";
                        $getGCHistory = $connection->fetchAll($query);

                        $giftcard_id = $getGCHistory[0]['giftcard_id'];
                        $gCHistory = $this->_giftCardHistoryFactory->create();

                        $dataHistory = [
                            'giftcard_id' => $giftcard_id,
                            'customer_id' => $customer_id,
                            'amount'      => $myattribute,
                            'action'      => 'create'
                        ];
                        $gCHistory->addData($dataHistory)->save();

                        //Insert into table Gift Card Customer Balance
                        $tablename = $connection->getTableName('mp_giftcard_customer_balance');
                        $query = "select customer_id from ".$tablename."
                        where customer_id='$customer_id'";
                        $getGCBalance = $connection->fetchAll($query);
                        if(empty($getGCBalance)) {
                            $gCCBalance = $this->_giftCardCustomerBalanceFactory->create();
                            $dataGCCBalance = [
                                'customer_id' => $customer_id,
                                'balance'     => 0
                            ];
                            $gCCBalance->addData($dataGCCBalance)->save();
                        }

                    }
                    $this->_conn->commit();
                }
                catch (Exception $e) {
                    $this->_conn->rollBack();
                    return $this;
                }
            }
        }

        return $this;
    }
}
