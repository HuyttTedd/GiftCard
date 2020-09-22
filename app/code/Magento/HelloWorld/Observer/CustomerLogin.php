<?php
namespace Magento\HelloWorld\Observer;

class CustomerLogin implements \Magento\Framework\Event\ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $a =  $observer->getData('password');
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/checkout.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r($a, true));
        //die();
        return $this;
    }
}
