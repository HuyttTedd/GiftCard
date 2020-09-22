<?php
namespace Magento\HelloWorld\Model;

class Post extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Magento\HelloWorld\Model\ResourceModel\Post::class);
    }

    public function getAbcd() {
        return 1234;
    }
}
