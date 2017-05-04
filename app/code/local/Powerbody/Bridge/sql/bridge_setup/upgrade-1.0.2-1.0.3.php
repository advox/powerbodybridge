<?php

/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/* @var $connection Varien_Db_Adapter_Interface */
$connection = $installer->getConnection();

$categoryTableName = 'powerbody_imported_category';
if (true !== $connection->isTableExists($categoryTableName)) {
    $table = $connection
        ->newTable($categoryTableName)
        ->addColumn(
            'id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            [
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'unsigned'  => true
            ]
        )
        ->addColumn(
            'powerbody_category_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            [
                'nullable'  => false,
                'unsigned'  => true
            ]
        )
        ->addColumn(
            'client_category_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            [
                'nullable'  => true,
                'unsigned'  => true
            ]
        )
        ->addColumn(
            'name',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            []
        )
        ->addColumn(
            'is_selected',
            Varien_Db_Ddl_Table::TYPE_SMALLINT,
            null,
            [
                'default'   => 0,
                'nullable'  => false
            ]
        )
        ->addColumn(
            'description',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            null,
            []
        )
        ->addColumn(
            'meta_title',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            []
        )
        ->addColumn(
            'meta_description',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            null,
            []
        )
        ->addColumn(
            'parent_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            [
                'nullable'  => false,
                'unsigned'  => true
            ]
        )
        ->addColumn(
            'path',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            []
        )
        ->addColumn(
            'position',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            [
                'nullable' => false
            ]
        )
        ->addColumn(
            'level',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            [
                'default'  => 0,
                'nullable' => false,
            ]
        )
        ->addColumn(
            'children_count',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            [
                'nullable' => false
            ]
        )
        ->addColumn(
            'created_date',
            Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            null,
            []
        )
        ->addColumn(
            'updated_date',
            Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            null,
            []
        );

    $connection->createTable($table);
}

$installer->endSetup();
