<?php
namespace Mageplaza\GiftCard\Model\ResourceModel;

class GiftCard extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('mageplaza_giftcard_code', 'giftcard_id');
    }

}
