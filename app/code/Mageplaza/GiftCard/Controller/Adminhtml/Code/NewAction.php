<?php
namespace Mageplaza\GiftCard\Controller\Adminhtml\Code;

class NewAction extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Mageplaza_GiftCard::mageplaza';
    protected $_resultForwardFactory = false;
    public function __construct
    (
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->_resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }

    public function execute()
    {
//        $resultPage = $this->_resultForwardFactory->create();
        return $this->_forward('edit');
//        return $resultPage;
    }
}
