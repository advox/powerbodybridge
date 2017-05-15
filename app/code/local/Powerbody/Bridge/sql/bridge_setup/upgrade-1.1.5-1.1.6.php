<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
/* @var $connection Varien_Db_Adapter_Interface */
$connection = $installer->getConnection();
$bridgeImportedManufacturerTableName = $connection->getTableName('bridge_imported_manufacturer');
$columnName = 'dropshipping_status';
$columnDataArray = [
    'comment'   => 'Dropshipping status',    
    'default'   => 1,
    'nullable'  => false,
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
];

if ($connection->isTableExists($bridgeImportedManufacturerTableName) === true) {
    $installer->getConnection()->addColumn($bridgeImportedManufacturerTableName, $columnName, $columnDataArray);
}


$installer->endSetup();
