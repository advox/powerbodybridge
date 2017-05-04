<?php

/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/* @var $connection Varien_Db_Adapter_Interface */
$connection = $installer->getConnection();

$tableName = $this->getTable('bridge_imported_manufacturer');
if (true === $connection->isTableExists($tableName)
    && true === $connection->tableColumnExists($tableName, 'client_manufacturer_id')
) {
    $connection->changeColumn($tableName, 'client_manufacturer_id', 'client_manufacturer_id', 'INT(10) NULL');
}

$installer->endSetup();
