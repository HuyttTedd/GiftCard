<?php
namespace Mageplaza\GiftCard\Controller\Customer;
use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action {
    protected $_customerSession;
    protected $helperData;

    public function __construct(
        Context $context,
        \Mageplaza\GiftCard\Helper\Data $helperData,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->_customerSession = $customerSession;
        $this->helperData = $helperData;
        parent::__construct($context);
    }

    public function execute() {
        $enableGC = $this->helperData->getGeneralConfig('enable_giftcard');
        if($this->_customerSession->isLoggedIn() && $enableGC == 1) {
            $this->_view->loadLayout();
            $this->_view->renderLayout();
        } else {
            return $this->_redirect('customer/account/index');
        }

    }
}
?>
