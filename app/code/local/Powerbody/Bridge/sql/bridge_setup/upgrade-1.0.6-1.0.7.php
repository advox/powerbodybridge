<?php

/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/* @var $connection Varien_Db_Adapter_Interface */
$connection = $installer->getConnection();

$tableName = $this->getTable('bridge_order_transfer_status');

if (false === $connection->isTableExists($tableName)) {
    $salesFlatOrderTable = $this->getTable('sales_flat_order');

    $table = $connection
        ->newTable($tableName)
        ->addColumn(
            'id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            [
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'unsigned'  => true,
            ]
        )
        ->addColumn(
            'order_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            [
                'nullable'  => false,
                'unsigned'  => true,
            ]
        )
        ->addColumn(
            'base_order_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            [
                'nullable'  => true,
                'unsigned'  => true,
            ]
        )
        ->addColumn(
            'status',
            Varien_Db_Ddl_Table::TYPE_SMALLINT,
            null,
            [
                'default'   => 0,
                'nullable'  => false,
            ]
        )
        ->addColumn(
            'created_at',
            Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            null,
            [
                'default'   => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
                'nullable'  => false,
            ]
        )
        ->addColumn(
            'updated_at',
            Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            null,
            []
        )
        ->addForeignKey(
            $this->getIdxName($tableName, 'order_id'),
            'order_id',
            $salesFlatOrderTable,
            'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        );

    $connection->createTable($table);
}

$installer->endSetup();
