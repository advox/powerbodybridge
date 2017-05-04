<?php

/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/* @var $connection Varien_Db_Adapter_Interface */
$connection = $installer->getConnection();

$tableName = $this->getTable('bridge_order_transfer_status');
$columnName = 'response_info';

if (true === $connection->isTableExists($tableName)
    && false === $connection->tableColumnExists($tableName, $columnName)
) {
    $connection->addColumn(
        $tableName,
        $columnName,
        [
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable'  => true,
            'comment'   => 'Response info',
        ]
    );
}

$installer->endSetup();
