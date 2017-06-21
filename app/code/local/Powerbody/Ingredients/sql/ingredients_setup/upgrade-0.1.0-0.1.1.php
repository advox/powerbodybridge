<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
/* @var $connection Varien_Db_Adapter_Interface */
$connection = $installer->getConnection();

$ingredientsProductLabelTable = $connection->getTableName('ingredients_product_label_image');
if ($connection->isTableExists($ingredientsProductLabelTable) === true) {
    $connection->dropTable($ingredientsProductLabelTable);
}

$ingredientsLabelTable = $connection->getTableName('ingredients_product_label');

if (false === $connection->isTableExists($ingredientsLabelTable)) {
    $ingredientsTable = $connection->newTable($ingredientsLabelTable)
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'nullable'  => false,
            'primary'   => true,
            'unsigned'  => true
        ))
        ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => true,
            'unsigned'  => true
        ))
        ->addColumn('path', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable'  => false,
        ))
        ->addColumn('filename', Varien_Db_Ddl_Table::TYPE_VARCHAR, 100, array(
            'nullable'  => false,
        ))
        ->addColumn('locale', Varien_Db_Ddl_Table::TYPE_VARCHAR, 10, array(
            'nullable'  => false,
        ))
        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'nullable'  => true,
            'unsigned'  => true
        ))
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable'  => true,
        ))
        ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable'  => true,
        ))
        ->addForeignKey(
            $this->getFkName($ingredientsLabelTable, 'product_id', $this->getTable('catalog/product'), 'entity_id'),
            'product_id',
            $this->getTable('catalog/product'),
            'entity_id',
            Varien_Db_Ddl_Table::ACTION_SET_NULL,
            Varien_Db_Ddl_Table::ACTION_SET_NULL
        );

    $installer->getConnection()->createTable($ingredientsTable);
}

$installer->endSetup();
