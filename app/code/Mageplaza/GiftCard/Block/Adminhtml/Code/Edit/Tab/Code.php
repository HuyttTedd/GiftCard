<?php
namespace Mageplaza\GiftCard\Block\Adminhtml\Code\Edit\Tab;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Mageplaza\GiftCard\Controller\RegistryConstants;

class Code extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $helperData;
    protected $registry;
    protected $giftCardFactory;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = [],
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Mageplaza\GiftCard\Helper\Data $helperData
    )
    {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->helperData = $helperData;
        $this->registry = $registry;
        $this->giftCardFactory = $giftCardFactory;
    }

    protected function _prepareForm()
    {
        $id = $this->registry->registry(RegistryConstants::CURRENT_GIFTCARD_ID);

        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Gift Card Information')]);

        if(!$id) {
            $fieldset->addField('codelength', 'text', [
                'name'     => 'codelength',
                'label'    => __('Code Length'),
                'title'    => __('Code Length'),
                'class'    => 'required-entry validate-digits validate-greater-than-zero',
                'required' => true,
                'value'    => $this->helperData->getCodeConfig('code_length'),
            ]);

            $fieldset->addField('balance', 'text', [
                'name'     => 'balance',
                'label'    => __('Balance'),
                'title'    => __('Balance'),
                'required' => true,
                'class'    => 'required-entry validate-number validate-greater-than-zero',
            ]);
        }



        if($id) {
            $giftcardModel = $this->giftCardFactory->create()->load($id);
            $code = $giftcardModel->getCode();
            $balance = $giftcardModel->getBalance();
            $createdFrom = $giftcardModel->getCreatedFrom();

            $fieldset->addField('giftcard_id', 'hidden', [
                'name'     => 'giftcard_id',
                'label'    => __('giftcard_id'),
                'title'    => __('giftcard_id'),
                'value'    => $id,
            ]);

            $fieldset->addField('code', 'text', [
                'name'     => 'code',
                'label'    => __('Code'),
                'title'    => __('Code'),
                'disabled' => true,
                'value'    => $code,
            ]);

            $fieldset->addField('balance', 'text', [
                'name'     => 'balance',
                'label'    => __('Balance'),
                'title'    => __('Balance'),
                'required' => true,
                'class'    => 'required-entry validate-number validate-greater-than-zero',
                'value'    => $balance,
            ]);

            $fieldset->addField('createdFrom', 'text', [
                'name'     => 'createdFrom',
                'label'    => __('Created From'),
                'title'    => __('Created From'),
                'disabled' => true,
                'value'    => $createdFrom,
            ]);
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }


    public function getTabLabel()
    {
        return __('Gift card information');
    }

    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
