<?php
/** @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
$connection = $installer->getConnection();
$emulation = new Mage_Core_Model_App_Emulation;
$emulation->startEnvironmentEmulation(Mage_Core_Model_App_Area::AREA_ADMIN);
$select = $connection->select()
    ->distinct(true)
    ->from($this->getTable('manufacturer_product'))
    ->reset(Zend_Db_Select::COLUMNS)
    ->columns(['product_id', 'manufacturer_id'])
;

$manufacturerProduct = $connection->fetchPairs($select);
$productIds = array_keys($manufacturerProduct);

/** @var Mage_Catalog_Model_Resource_Product_Collection $productCollection */
$productCollection = Mage::getModel('catalog/product')->getCollection();
$productCollection->addAttributeToFilter('is_imported', true);
$productCollection->addIdFilter($productIds);

foreach ($productCollection as $productModel) {
    /** @var $productModel Mage_Catalog_Model_Product */
    $productModel = $productModel->load($productModel->getId());
    if (true === isset($manufacturerProduct[$productModel->getId()])) {
        $productModel->setData('manufacturer', $manufacturerProduct[$productModel->getId()]);
        $productModel->getResource()->saveAttribute($productModel,'manufacturer');
    }
}

$installer->endSetup();
