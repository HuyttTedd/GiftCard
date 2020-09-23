<?php
namespace Magento\HelloWorld\Controller\Model;

use Magento\Framework\App\Action\Context;
use Magento\HelloWorld\Model\Post;

class GetList extends \Magento\Framework\App\Action\Action
{
    protected $_postFactory;

    public function __construct(
        Context $context,
        \Magento\HelloWorld\Model\PostFactory $postFactory
    )
    {
        $this->_postFactory = $postFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var Post $post */
        $post = $this->_postFactory->create();
        $post->load(2);
        echo "<pre>";
        print_r($post->load(2)->getData('post_id'));
        echo "</pre>";
        $collection = $post->getCollection();
        //in cau SQL
        //echo $collection->getSelect()->__toString();
//        echo "<pre>";
//        print_r($collection->getData());
//        echo "</pre>";

    }
}
