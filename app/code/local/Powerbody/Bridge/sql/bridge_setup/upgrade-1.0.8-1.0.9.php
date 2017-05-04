<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/* @var $connection Varien_Db_Adapter_Interface */
$connection = $installer->getConnection();

$connection->dropColumn($connection->getTableName('manufacturer_store'), 'product_count');

$installer->endSetup();
