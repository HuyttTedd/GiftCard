<?php
namespace Magento\HelloWorld\Controller\Index;

class TestEvent extends \Magento\Framework\App\Action\Action
{

    public function execute()
    {
        $textDisplay = new \Magento\Framework\DataObject(array('text' => 'Mageplaza'));
        $this->_eventManager->dispatch('magento_helloworld_display_text', ['mp_text' => $textDisplay]);
        echo "<br>".$textDisplay->getText()."12<br>";
        exit;
    }
}
