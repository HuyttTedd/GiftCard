<?php
namespace Mageplaza\GiftCard\Controller\Adminhtml\Code;
use Mageplaza\GiftCard\Controller\RegistryConstants;
class Edit extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Mageplaza_GiftCard::mageplaza';

    protected $_resultPageFactory;
    protected $_coreRegistry;
    protected $_giftCardFactory;

    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_giftCardFactory = $giftCardFactory;
        parent::__construct($context);
    }


    public function execute()
    {

        $giftcardId = $this->initCurrentGiftCard();
        $isExistingGiftCard = (bool)$giftcardId;
        $resultPage = $this->_resultPageFactory->create();
        $giftcardModel = $this->_giftCardFactory->create();
        $giftcardModel->load($giftcardId);
        if($isExistingGiftCard && $giftcardModel->getGiftcardId()) {
            $code = $giftcardModel->getCode();
            $resultPage->getConfig()->getTitle()->prepend("Gift Card ".$code);
        }elseif ($isExistingGiftCard && !$giftcardModel->getGiftcardId()) {
            $this->_redirect('*/*/index');
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Gift Card'));
        }
        return $resultPage;
    }

    protected function initCurrentGiftCard()
    {
        $giftcardId = (int)$this->getRequest()->getParam('id');

        if ($giftcardId) {
            $this->_coreRegistry->register(RegistryConstants::CURRENT_GIFTCARD_ID, $giftcardId);
        }
        return $giftcardId;
    }
}
