<?php

/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/* @var $connection Varien_Db_Adapter_Interface */
$connection = $installer->getConnection();

$manufacturerTableName = 'powerbody_imported_manufacturer';
if (true !== $connection->isTableExists($manufacturerTableName)) {
    $table = $connection
        ->newTable($manufacturerTableName)
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
            'powerbody_manufacturer_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            [                
                'nullable'  => false,             
                'unsigned'  => true
            ]
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
            'client_manufacturer_id',
            Varien_Db_Ddl_Table::TYPE_SMALLINT, 
            null, 
            [
                'default'   => 0,
                'nullable'  => false
            ]            
        )    
        ->addColumn(
            'name',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            []
        )
        ->addColumn(
            'description',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            null,
            []
        )
        ->addColumn(
            'logo',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            50,
            []
        )
        ->addColumn(
            'logo_normal',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            50,
            []
        )
        ->addColumn(
            'priority',
            Varien_Db_Ddl_Table::TYPE_SMALLINT,
            null,
            [
                'default'   => 0,
                'nullable'  => false,
                'unsigned'  => true,
            ]
        )
        ->addColumn(
            'brand_to_withdraw',
            Varien_Db_Ddl_Table::TYPE_TINYINT,
            null,
            [
                'default'   => 0,
                'nullable'  => false,
                'unsigned'  => true,
            ]
        )
        ->addColumn(
            'is_visible_on_front',
            Varien_Db_Ddl_Table::TYPE_TINYINT,
            null,
            [
                'default'   => 1,
                'nullable'  => false,
                'unsigned'  => true,
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
