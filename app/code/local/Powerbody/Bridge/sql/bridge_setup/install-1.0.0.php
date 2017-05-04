<?php

/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$tableName = $this->getTable('bridge_service_log');
if ($installer->getConnection()->isTableExists($tableName) === true) {
    $installer->getConnection()->dropTable($tableName);
}
$table = $installer->getConnection()->newTable($tableName)
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        'unsigned'  => true,
    ))
    ->addColumn('api_user_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array())
    ->addColumn('api_method_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array())
    ->addColumn('params', Varien_Db_Ddl_Table::TYPE_TEXT, null, array())
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true
    ))
    ->addColumn('info', Varien_Db_Ddl_Table::TYPE_TEXT, null,  array())
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'default'   => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
        'nullable'  => false,
    ));
$installer->getConnection()->createTable($table);

$tableName = $this->getTable('bridge_sync_log');
if ($installer->getConnection()->isTableExists($tableName) === true) {
    $installer->getConnection()->dropTable($tableName);
}
$table = $installer->getConnection()->newTable($tableName)
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        'unsigned'  => true,
    ))
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'unsigned' => true
    ))
    ->addColumn('action', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true
    ))
    ->addColumn('message', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true
    ))
    ->addColumn('trace', Varien_Db_Ddl_Table::TYPE_TEXT, null,  array())
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'default'   => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
        'nullable'  => false,
    ));
$installer->getConnection()->createTable($table);

$installer->endSetup();
