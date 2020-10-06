<?php
namespace Mageplaza\GiftCard\Controller\Adminhtml\Code;

use Magento\Setup\Exception;

class Save extends \Magento\Backend\App\Action
{
    protected $_giftCardFactory;
    protected $_request;
    protected $_messageManager;
    protected $_mathRandom;
    const ADMIN_RESOURCE = 'Mageplaza_GiftCard::mageplaza';

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Mageplaza\GiftCard\Model\GiftCardFactory $_giftCardFactory,
        \Magento\Framework\App\Request\Http $_request,
        \Magento\Framework\Math\Random $_mathRandom
    )
    {
        $this->_giftCardFactory = $_giftCardFactory;
        $this->_messageManager = $messageManager;
        $this->_request = $_request;
        $this->_mathRandom = $_mathRandom;
        parent::__construct($context);
    }

    public function execute()
    {
        $param = $this->getRequest()->getParams();
        $chars = 'ABCDEFGHIJKLMLOPQRSTUVXYZ0123456789';


        if (isset($param['delete'])) {
            try {
                $id = $param['id'];
                $giftcardModel = $this->_giftCardFactory->create();
                $giftcardModel->load($id)->delete();
                $this->_messageManager->addSuccessMessage('Delete Gift Card successfully!');

            } catch (Exception $e) {
                $this->_messageManager->addErrorMessage('Delete Gift Card failed!');
            }
            return $this->_redirect('*/*/index');
        }
        if(isset($param['giftcard_id'])) {
            try {
                $id = $param['giftcard_id'];
                $giftcardModel = $this->_giftCardFactory->create();
                $getGC = $giftcardModel->load($id)->toArray();
                if(!empty($getGC)) {
                    $amountUsed = $getGC->getData('amount_used');
                    if($amountUsed > $param['balance']) {
                        $this->_messageManager->addErrorMessage('Amount used must not be greater than balance!');
                        return $this->_redirect("*/*/edit/id/$id");
                    } else {
                        $getGC->setBalance($param['balance'])->save();
                        $this->_messageManager->addSuccessMessage('Edit Gift Card successfully!');
                    }
                } else {
                    $this->_messageManager->addErrorMessage('Edit Gift Card failed!');
                    return $this->_redirect('*/*/index');
                }

            } catch (Exception $e) {
                $this->_messageManager->addErrorMessage('Edit Gift Card failed!');
            }
                if(isset($param['back'])) {
                    $id = $param['giftcard_id'];
                    return $this->_redirect("*/*/edit/id/$id");
                } else {
                    return $this->_redirect('*/*/index');
                }

        } else {
            try {
                $code = $this->_mathRandom->getRandomString($param['codelength'], $chars);
                $balance = $param['balance'];
                $data = [
                    'code' => $code,
                    'balance' => $balance,
                    'amount_used' => 0,
                    'created_from' => 'admin'
                ];

                $giftcardModel = $this->_giftCardFactory->create();

                $giftcardModel->addData($data)->save();
                $gc_id = $giftcardModel->load($code,'code')->getData('giftcard_id');

                $this->_messageManager->addSuccessMessage('Add Gift Card successfully!');
                if(isset($param['back']) && isset($gc_id)) {
                    return $this->_redirect("*/*/edit/id/$gc_id");
                } else {
                    return $this->_redirect('*/*/index');
                }



            } catch (Exception $e) {
                $this->_messageManager->addErrorMessage('Add Gift Card failed!');
            }
        }
    }
}

