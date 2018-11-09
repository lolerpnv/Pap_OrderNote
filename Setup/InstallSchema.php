<?php

namespace Pap\OrderNote\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Module installation script
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Creating order note table
     * Adding columns to
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {

        $setup->startSetup();

        $orderNote = $setup->getConnection()->newTable('pap_order_note');

        $orderNote->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
            ],
            'ID'
        );
        $orderNote->addColumn(
            'order_id',
            Table::TYPE_INTEGER,
            null,
            [
                'unsigned' => true,
                'nullable' => false
            ],
            'ID'
        );

        $orderNote->addForeignKey(
            $setup->getFkName('sales_order', 'entity_id', 'pap_order_note', 'entity_id'),
            'order_id',
            'sales_order',
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        $orderNote->addColumn(
            'note',
            Table::TYPE_TEXT,
            100,
            [
                'nullable' => true,
            ],
            'note text'
        );

        $orderNote->setComment('Order note');

        $setup->getConnection()->createTable($orderNote);

        //extending sales order grid to store note
        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order_grid'),
            'customer_note',
            [
                'type' => Table::TYPE_TEXT,
                'comment' => 'Customer Note'
            ]
        );

        // Adding shipment column , to keep track whether it was exported
        $setup->getConnection()->addColumn(
            $setup->getTable('sales_shipment'),
            'exported',
            [
                'type' => Table::TYPE_BOOLEAN,
                'default' => false,
                'nullable' => false,
                'comment' => 'Is exported'
            ]
        );


        $setup->endSetup();
    }
}
