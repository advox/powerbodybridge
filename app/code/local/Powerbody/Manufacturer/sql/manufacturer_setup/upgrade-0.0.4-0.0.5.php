<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
/* @var $connection Varien_Db_Adapter_Interface */
$connection = $installer->getConnection();
$manufacturerTableName = 'manufacturer';
if ($connection->isTableExists($manufacturerTableName) === true) {
    $installer->getConnection()
        ->addColumn($manufacturerTableName, 'margin', array(
            'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'nullable'  => false,
            'default'   => 0,
            'length'    => 4,
            'after'     => null,
            'comment'   => 'Margin percentage'
        ));
}
$installer->endSetup();
