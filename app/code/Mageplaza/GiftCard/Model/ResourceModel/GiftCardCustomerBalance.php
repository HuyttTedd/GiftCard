<?php
namespace Mageplaza\GiftCard\Model\ResourceModel;

class GiftCardCustomerBalance extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected $_isPkAutoIncrement = false;

    protected function _construct()
    {
        $this->_init('giftcard_customer_balance', 'customer_id');
    }

}
