<?php
namespace Mageplaza\GiftCard\Block\Adminhtml\Code;
use Mageplaza\GiftCard\Controller\RegistryConstants;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected $_coreRegistry;

    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    )
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->_objectId = 'giftcard_id';
        $this->_blockGroup = 'Mageplaza_GiftCard';
        $this->_controller = 'adminhtml_code';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Gift Card'));
   }

    protected function _prepareLayout()
    {
        $giftcardId = $this->getGiftCardId();
        //if (!$giftcardId) {
            $this->buttonList->add(
                'save_and_continue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']],
                    ],

                    'onclick' => $this->_getSaveAndContinueUrl()
                ],
                10
            );
        //}
        if ($giftcardId) {
            $this->buttonList->add(
                'delete',
                [
                    'label' => __('Delete'),
                    'class' => 'delete',
                    'onclick' => 'deleteConfirm(\'' . __(
                            'Are you sure you want to do this?'
                        ) . '\', \'' . $this->getDeleteUrl() . '\', {data: {}})'
                ],
                3
            );
        }
        return parent::_prepareLayout();
    }

    public function getGiftCardId()
    {
        $giftcardId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_GIFTCARD_ID);
        return $giftcardId;
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('giftcard/code/save', ['_current' => true, 'delete' => 1]);
    }

    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl(
            'giftcard/code/save',
            ['_current' => true, 'back' => 'edit']
        );
    }

}
