<?php
namespace Mageplaza\GiftCard\Observer;

class PalaceOrder implements \Magento\Framework\Event\ObserverInterface
{
    protected $_giftCardHistoryFactory;
    protected $_giftCardCustomerBalanceFactory;
    protected $_giftCardFactory;
    protected $_resource;
    protected $_helperData;
    protected $_mathRandom;

    public function __construct(
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Mageplaza\GiftCard\Model\GiftCardHistoryFactory $giftCardHistoryFactory,
        \Mageplaza\GiftCard\Model\GiftCardCustomerBalanceFactory $giftCardCustomerBalanceFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Mageplaza\GiftCard\Helper\Data $helperData,
        \Magento\Framework\Math\Random $mathRandom
    )
    {
        $this->_giftCardFactory = $giftCardFactory;
        $this->_giftCardHistoryFactory = $giftCardHistoryFactory;
        $this->_giftCardCustomerBalanceFactory = $giftCardCustomerBalanceFactory;
        $this->_resource = $resource;
        $this->_helperData = $helperData;
        $this->_mathRandom = $mathRandom;
    }


    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getData('order');
        $order_id = $order->getEntityId();

        $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $tablename = $connection->getTableName('mp_sales_order_item');
        $query = "select sku, is_virtual, qty_ordered from ".$tablename." where order_id = '$order_id'";
        $getToCheck = $connection->fetchAll($query);

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/checkLog.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r($getToCheck));
        foreach ($getToCheck as $key => $value) {
            if($value['sku'] == 'Gift Card' && $value['is_virtual'] == 1) {
                $chars = 'ABCDEFGHIJKLMLOPQRSTUVXYZ0123456789';
                $qtyGiftCard = $value['qty_ordered'];
                $codelength = $this->_helperData->getCodeConfig('code_length');
                $order_increment_id = $order->getIncrementId();
                for($i = 0; $i < $qtyGiftCard; $i++) {
                    $code = $this->_mathRandom->getRandomString($codelength, $chars);

                }
            }
        }
    }
}
