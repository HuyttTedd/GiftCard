<?php
namespace Mageplaza\GiftCard\Controller\Index;

use \Magento\Framework\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use \Magento\Framework\App\Action\Action;
use \Magento\Framework\App\Request\Http;

class Test extends Action
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
        $param = $this->getRequest()->getParams();
        $code = $this->_giftCardFactory->create();
        //$this->getList($code);

        //$this->deleteGiftCard($code, $param['id']);
        //id/3

        $this->addGiftCard($code, $param);
        //code/DFCVFGRF344E/balance/100/amount_used/0/created_from/admin

        //$this->editGiftCard($code, $param);
        //id/3/code/SKDCMI9989JH/balance/80/amount_used/70
    }

    public function getList($code) {
        $listCodes = $code->getCollection()->getData();
        echo "<pre>";
        print_r($listCodes);
        echo "</pre>";
    }

    public function addGiftCard($code, $param) {
       $code->addData($param)->save();
    }

    public function editGiftCard($code, $param) {
        $id = $param['id'];
        array_shift($param);
        foreach ($param as $key => $val) {
            $code->load($id)->setData($key, $val)->save();
        }

    }

    public function deleteGiftCard($code ,$id) {
        $code->load($id)->delete();
    }
}
