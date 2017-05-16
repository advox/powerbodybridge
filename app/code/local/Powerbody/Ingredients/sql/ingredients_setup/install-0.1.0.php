<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$tableName = $this->getTable('ingredients/product_label_image');
$installer->getConnection()->dropTable($tableName);

$table = $installer->getConnection()->newTable($tableName)
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  =>  true,
        'nullable'  =>  false,
        'primary'   =>  true,
        'unsigned'  =>  true
    ))
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  =>  false,
        'unsigned'  =>  true
    ))
    ->addColumn('image', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  =>  false,
        'unsigned'  =>  true
    ))
    ->addColumn('date_modified', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable'  =>  false,
    ))
    ->addIndex(
        $this->getIdxName($this->getTable($tableName), 'product_id'),
        'product_id'
    )
    ->addForeignKey(
        $this->getIdxName($this->getTable($tableName), 'product_id'),
        'product_id',
        $this->getTable($this->getTable('catalog_product_entity')),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_NO_ACTION,
        Varien_Db_Ddl_Table::ACTION_NO_ACTION
    );

$installer->getConnection()->createTable($table);
$installer->endSetup();
