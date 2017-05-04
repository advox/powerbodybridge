<?php

/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$manufacturerTableName = $this->getTable('manufacturer');
if (true !== $installer->getConnection()->isTableExists($manufacturerTableName)) {
    $table = $installer->getConnection()
        ->newTable($manufacturerTableName)
        ->addColumn(
            'id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'unsigned'  => true
            )
        )
        ->addColumn(
            'name',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array()
        )
        ->addColumn(
            'description',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            null,
            array()
        )
        ->addColumn(
            'logo',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            50,
            array()
        )
        ->addColumn(
            'logo_normal',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            50,
            array()
        )
        ->addColumn(
            'priority',
            Varien_Db_Ddl_Table::TYPE_SMALLINT,
            null,
            array(
                'default'   => 0,
                'nullable'  => false,
                'unsigned'  => true,
            )
        )
        ->addColumn(
            'brand_to_withdraw',
            Varien_Db_Ddl_Table::TYPE_TINYINT,
            null,
            array(
                'default'   => 0,
                'nullable'  => false,
                'unsigned'  => true,
            )
        )
        ->addColumn(
            'is_visible_on_front',
            Varien_Db_Ddl_Table::TYPE_TINYINT,
            null,
            array(
                'default'   => 1,
                'nullable'  => false,
                'unsigned'  => true,
            )
        )
        ->addColumn(
            'created_date',
            Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            null,
            array()
        )
        ->addColumn(
            'updated_date',
            Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            null,
            array()
        );

    $installer->getConnection()->createTable($table);
}

$manufacturerStoreTableName = $this->getTable('manufacturer_store');
if (true !== $installer->getConnection()->isTableExists($manufacturerStoreTableName)) {
    $table = $installer->getConnection()
        ->newTable($manufacturerStoreTableName)
        ->addColumn(
            'id', 
            Varien_Db_Ddl_Table::TYPE_INTEGER, 
            null, 
            array(
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'unsigned'  => true,
            )
        )
        ->addColumn(
            'manufacturer_id',
             Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'nullable'  => false,
                'unsigned'  => true,
            )
        )
        ->addColumn(
            'store_id',
             Varien_Db_Ddl_Table::TYPE_SMALLINT,
            null,
            array(
                'nullable'  => false,
                'unsigned'  => true,
            )
        )
        ->addColumn(
            'url_key',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            100,
            array()
        )
        ->addColumn(
            'product_count',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'default'   => 0,
                'nullable'  => false,
                'unsigned'  => true,
            )
        )
        ->addForeignKey(
            $this->getFkName(
                $manufacturerStoreTableName,
                'manufacturer_id',
                $this->getTable('manufacturer'),
                'id'
            ),
            'manufacturer_id',
            $this->getTable('manufacturer'),
            'id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->addForeignKey(
            $this->getFkName(
                $manufacturerStoreTableName,
                'store_id',
                $this->getTable('core_store'),
                'store_id'
            ),
            'store_id',
            $this->getTable('core_store'),
            'store_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        );
    
    $installer->getConnection()->createTable($table);
}

$manufacturerProductTableName = $this->getTable('manufacturer_product');
if (true !== $installer->getConnection()->isTableExists($manufacturerProductTableName)) {
    $table = $installer->getConnection()
        ->newTable($manufacturerProductTableName)
        ->addColumn(
            'id',
             Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'unsigned'  => true,
            )
        )
        ->addColumn(
            'product_id',
             Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'nullable'  => false,
                'unsigned'  => true,
            )
        )
        ->addColumn(
            'manufacturer_id',
             Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'nullable'  => false,
                'unsigned'  => true,
            )
        )
        ->addForeignKey(
            $this->getFkName(
                $manufacturerProductTableName, 
                'product_id',
                $this->getTable('catalog_product_entity'), 
                'entity_id'
            ),
            'product_id',
            $this->getTable('catalog_product_entity'),
            'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->addForeignKey(
            $this->getFkName(
                $manufacturerProductTableName,
                'manufacturer_id',
                $this->getTable('manufacturer'),
                'id'
            ),
            'manufacturer_id',
            $this->getTable('manufacturer'),
            'id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        );

    $installer->getConnection()->createTable($table);
}

$installer->endSetup();
