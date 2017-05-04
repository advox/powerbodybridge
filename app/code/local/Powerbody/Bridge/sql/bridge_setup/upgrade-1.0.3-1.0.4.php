<?php

/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/* @var $connection Varien_Db_Adapter_Interface */
$connection = $installer->getConnection();

$oldTableName = 'powerbody_imported_category';
$newTableName = 'bridge_imported_category';

if (true === $connection->isTableExists($oldTableName)
    && false === $connection->isTableExists($newTableName) 
) {
    $connection->renameTable($oldTableName, $newTableName);
    $connection->dropColumn($newTableName, 'description');
    $connection->dropColumn($newTableName, 'meta_title');
    $connection->dropColumn($newTableName, 'meta_description');
    $connection->dropColumn($newTableName, 'position');
    $connection->dropColumn($newTableName, 'level');
    $connection->dropColumn($newTableName, 'children_count');
    $connection->changeColumn($newTableName, 'powerbody_category_id', 'base_category_id', 'INT(10) NOT NULL');
}
$connection->dropTable($oldTableName);

$installer->endSetup();
