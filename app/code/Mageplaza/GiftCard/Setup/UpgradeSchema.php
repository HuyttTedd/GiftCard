<?php
namespace Mageplaza\GiftCard\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade( SchemaSetupInterface $setup, ModuleContextInterface $context ) {
        $installer = $setup;

        $installer->startSetup();

        if(version_compare($context->getVersion(), '2.0.2', '<')) {
            if (!$installer->tableExists('giftcard_history')) {
                $table = $installer->getConnection()->newTable(
                    $installer->getTable('giftcard_history')
                )
                    ->addColumn(
                        'history_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        [
                            'identity' => true,
                            'nullable' => false,
                            'primary'  => true,
                            'unsigned' => true,
                        ],
                        'History ID'
                    )
                    ->addColumn(
                        'giftcard_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        [   'nullable' => false,
                            'unsigned' => true,],
                        'Gift Card ID'
                    )
                    ->addColumn(
                        'customer_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        [   'nullable' => false,
                            'unsigned' => true,],
                        'Customer ID'
                    )
                    ->addColumn(
                        'amount',
                        \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        '12,4',
                        [
                            'nullable' => false
                        ],
                        'Amount Changed'
                    )
                    ->addColumn(
                        'action',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        ['nullable' => false],
                        'create/redeem/Used for order'
                    )
                    ->addColumn(
                        'action_time',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                        'Time occur'
                    )
                    ->addForeignKey(
                        $installer->getFkName('giftcard_history', 'giftcard_id', 'mageplaza_giftcard_code', 'giftcard_id'),
                        'giftcard_id',
                        $installer->getTable('mageplaza_giftcard_code'),
                        'giftcard_id',
                        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $installer->getFkName('giftcard_history', 'customer_id', 'customer_entity', 'entity_id'),
                        'customer_id',
                        $installer->getTable('customer_entity'),
                        'entity_id',
                        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )
                    ->setComment('Gift Card History Table');
                $installer->getConnection()->createTable($table);
            }

            if (!$installer->tableExists('giftcard_customer_balance')) {
                $table = $installer->getConnection()->newTable(
                    $installer->getTable('giftcard_customer_balance')
                )
                    ->addColumn(
                        'customer_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        [   'nullable' => false,
                            'unsigned' => true,],
                        'Customer ID'
                    )
                    ->addColumn(
                        'balance',
                        \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        '12,4',
                        [
                            'nullable' => false
                        ],
                        'Balance'
                    )
                    ->addForeignKey(
                        $installer->getFkName('giftcard_customer_balance', 'customer_id', 'customer_entity', 'entity_id'),
                        'customer_id',
                        $installer->getTable('customer_entity'),
                        'entity_id',
                        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )
                    ->setComment('Gift Card Customer Balance Table');
                $installer->getConnection()->createTable($table);
            }
            if($installer->getConnection()->isTableExists('mp_quote')) {
                $installer->getConnection()->addColumn(
                    'mp_quote',
                    'giftcard_code',
                    [   'type'=>\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'size' => 255,
                        'nullable'=> true,
                        'comment' => 'Gift Card Code']
                );
                $installer->getConnection()->addColumn(
                    'mp_quote',
                    'giftcard_base_discount',
                    [   'type'=>\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'size' => null,
                        'length' => '12,4',
                        'nullable'=> true,
                        'comment' => 'Gift Card Base Discount']
                );
                $installer->getConnection()->addColumn(
                    'mp_quote',
                    'giftcard_discount',
                    [   'type'=>\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'size' => null,
                        'length' => '12,4',
                        'nullable'=> true,
                        'comment' => 'Gift Card Discount']
                );
            }
        }
        $installer->endSetup();
    }
}
