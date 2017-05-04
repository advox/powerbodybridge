<?php

require_once 'app/Mage.php';
Varien_Profiler::enable();
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);
Mage::app('admin');
Mage::register('isSecureArea', 1);
Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);

/* @var $productSyncModel Powerbody_Bridge_Model_Sync_Product */
$productSyncModel = Mage::getModel('bridge/sync_product');
$productsDataArray = $productSyncModel->getProductsImportDataForCurrentProducts();

if (true === isset($productsDataArray['success']) && true === $productsDataArray['success']) {
    $productsDataArray = $productsDataArray['data'];
    foreach ($productsDataArray as $productDataArray) {

        try {
            /* @var $productModel Mage_Catalog_Model_Product */
            $productModel = Mage::getModel('catalog/product')->load($productDataArray['entity_id']);
            $productModel->addData(['ean' => $productDataArray['ean']]);
            $productModel->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }
}
