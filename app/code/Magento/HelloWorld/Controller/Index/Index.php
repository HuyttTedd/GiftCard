<?php
namespace Magento\HelloWorld\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{

    public function execute()
    {
        $arr = ['test1'=>'123', 'test2'=>'123', 'test3'=>'123'];
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/checkout.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r($arr, true));
        //domain/helloworld/index/index
        //domain/frontname/folder/action
        //$this->_forward('test');
        echo 'Hello World';
    }
}
