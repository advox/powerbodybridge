<?php

/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/* @var $connection Varien_Db_Adapter_Interface */
$connection = $installer->getConnection();

$oldTableName = 'powerbody_imported_manufacturer';
$newTableName = 'bridge_imported_manufacturer';

if (true === $connection->isTableExists($oldTableName)
    && false === $connection->isTableExists($newTableName)
) {
    $connection->renameTable($oldTableName, $newTableName);
    $connection->dropColumn($newTableName, 'description');
    $connection->dropColumn($newTableName, 'logo');
    $connection->dropColumn($newTableName, 'logo_normal');
    $connection->dropColumn($newTableName, 'priority');
    $connection->dropColumn($newTableName, 'brand_to_withdraw');
    $connection->dropColumn($newTableName, 'is_visible_on_front');
    $connection->changeColumn($newTableName, 'powerbody_manufacturer_id', 'base_manufacturer_id', 'INT(10) NOT NULL');
}
$connection->dropTable($oldTableName);

$installer->endSetup();
