<?php

namespace Mageplaza\GiftCard\Controller\Customer;

use Magento\Setup\Exception;

class Redeem extends \Magento\Framework\App\Action\Action
{

    protected $helperData;
    protected $_giftCardFactory;
    protected $_messageManager;
    protected $_customerSession;
    protected $_giftCardHistoryFactory;
    protected $_giftCardCustomerBalanceFactory;
    protected $_resource;
    protected $_transaction;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Mageplaza\GiftCard\Model\GiftCardHistoryFactory $giftCardHistoryFactory,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Model\Session $customerSession,
        \Mageplaza\GiftCard\Helper\Data $helperData,
        \Magento\Framework\App\ResourceConnection $resource,
        \Mageplaza\GiftCard\Model\GiftCardCustomerBalanceFactory $giftCardCustomerBalanceFactory


    )
    {
        $this->_giftCardHistoryFactory = $giftCardHistoryFactory;
        $this->_customerSession = $customerSession;
        $this->_messageManager = $messageManager;
        $this->helperData = $helperData;
        $this->_resource = $resource;
        $this->_giftCardFactory = $giftCardFactory;
        $this->_giftCardCustomerBalanceFactory = $giftCardCustomerBalanceFactory;
        parent::__construct($context);

    }

    public function execute()
    {
        $allowRedeem = $this->helperData->getGeneralConfig('allow_redeem');
        $enableGC = $this->helperData->getGeneralConfig('enable_giftcard');
        $param = $this->getRequest()->getParams();
        if($allowRedeem == 1 && $enableGC == 1 && isset($param['giftcard_code'])) {
            try {
                $code = $param['giftcard_code'];
                $customer_id = $this->_customerSession->getCustomer()->getId();
                $giftcardModel = $this->_giftCardFactory->create();
                $gCcode = $giftcardModel->getCollection()->addFilter('code', $code)->getFirstItem()->toArray();

                if(empty($gCcode)) {
                    $this->_messageManager->addErrorMessage('Gift Card does not exist!');
                    return $this->_redirect('giftcard/customer/index');
                } else {
                    if($gCcode['balance'] > $gCcode['amount_used']) {
                        $this->_transaction = $this->_resource->getConnection()->beginTransaction();
                        $giftcard_id = $gCcode["giftcard_id"];
                        $codeRedeem = $giftcardModel->load($giftcard_id);
                        $codeRedeem->setData('amount_used', $gCcode['balance'])->save();
                        //die('');
                        //insert vao bang History
                        $amount_change = (int)$gCcode['balance'] - (int)$gCcode['amount_used'];
                        $dataHistory = [
                            'giftcard_id' => $giftcard_id,
                            'customer_id' => $customer_id,
                            'amount'      => $amount_change,
                            'action'      => 'Redeem'
                        ];
                        $this->_giftCardHistoryFactory->create()->addData($dataHistory)->save();

                        //insert vào balance của customer check balance = balance - amount_used
                        $gCCBalanceModel = $this->_giftCardCustomerBalanceFactory->create();
                        $getInforGC = $gCCBalanceModel->load($customer_id);
                        if($getInforGC) {
                            $balanceCus = $getInforGC->getData('balance');
                            $newBalance = (int)$amount_change + (int)$balanceCus;
                            $getInforGC->setData('balance', $newBalance)->save();
                        } else {
                            $dataCusBalance = [
                                'customer_id' => $customer_id,
                                'balance'     => $amount_change
                            ];
                            $gCCBalanceModel->addData($dataCusBalance)->save();
                        }

                        $this->_transaction->commit();
                        $this->_messageManager->addSuccessMessage('Redeem Gift Card successfully!');
                        return $this->_redirect('giftcard/customer/index');

                    } else {
                        $this->_messageManager->addErrorMessage('Gift Card has no money left!');
                        return $this->_redirect('giftcard/customer/index');
                    }

                }

            } catch (Exception $e) {
                        $this->_messageManager->addErrorMessage('Somthing went wrong!');
                        return $this->_redirect('giftcard/customer/index');
            }
        } else {
            $this->_messageManager->addErrorMessage('Somthing went wrong!');
            return $this->_redirect('giftcard/customer/index');
        }
    }
}
