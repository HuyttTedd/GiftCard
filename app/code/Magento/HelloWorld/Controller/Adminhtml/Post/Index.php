<?php
namespace Magento\HelloWorld\Controller\Adminhtml\Post;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory = false;
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_HelloWorld::helloworld1';

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }


    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('Posts')));

        return $resultPage;
    }


}
