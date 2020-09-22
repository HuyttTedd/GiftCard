<?php
namespace Mageplaza\GiftCard\Model\ResourceModel\GiftCardHistory;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'giftcard_id';

    protected function _construct()
    {
        $this->_init(\Mageplaza\GiftCard\Model\GiftCardHistory::class, \Mageplaza\GiftCard\Model\ResourceModel\GiftCardHistory::class);
    }
}
