<?php
namespace Mageplaza\GiftCard\Model\ResourceModel\GiftCardCustomerBalance;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected function _construct()
    {
        $this->_init(\Mageplaza\GiftCard\Model\GiftCardCustomerBalance::class, \Mageplaza\GiftCard\Model\ResourceModel\GiftCardCustomerBalance::class);
    }
}
