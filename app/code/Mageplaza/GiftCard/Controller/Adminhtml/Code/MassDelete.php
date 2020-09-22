<?php
namespace Mageplaza\GiftCard\Controller\Adminhtml\Code;

class MassDelete extends \Magento\Backend\App\Action {

    protected $_filter;
    const ADMIN_RESOURCE = 'Mageplaza_GiftCard::mageplaza';
    protected $_collectionFactory;

    public function __construct(
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Mageplaza\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory $collectionFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->_filter            = $filter;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    public function execute() {
        try{

            $logCollection = $this->_filter->getCollection($this->_collectionFactory->create());
            //echo "<pre>";
            //print_r($logCollection->getData());
            //exit;
            foreach ($logCollection as $item) {
                $item->delete();
            }
            $this->messageManager->addSuccess(__('Log Deleted Successfully.'));
        }catch(Exception $e){
            $this->messageManager->addError($e->getMessage());
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('giftcard/code/index'); //Redirect Path
    }

}
