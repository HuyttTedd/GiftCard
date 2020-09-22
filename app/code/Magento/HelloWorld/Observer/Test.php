<?php
namespace Magento\HelloWorld\Observer;
use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;

class Test implements ObserverInterface
{
    public function execute(Observer $observer)
    {
//        $displayText = $observer->getMpText();
//        print_r($displayText);
    }
}
