<?php
namespace Magento\HelloWorld\Controller\Index;

use \Magento\Framework\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use \Magento\Framework\App\Action\Action;
use \Magento\Framework\App\Request\Http;

class Test3 extends Action
{
    protected $_pageFactory;
    protected $_request;
    protected $_giftCardFactory;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        Http $_request,
        \Mageplaza\GiftCard\Model\GiftCardFactory $_giftCardFactory
    )
    {
        $this->_giftCardFactory = $_giftCardFactory;
        $this->_request = $_request;
        $this->_pageFactory = $pageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $param = $this->getRequest()->getPostValue('age');
        print_r($param);

        //
        //id/3


        //code/DFCVFGRF344E/balance/100/amount_used/0/created_from/admin

        //$this->editGiftCard($code, $param);
        //id/3/code/SKDCMI9989JH/balance/80/amount_used/70
    }


}
