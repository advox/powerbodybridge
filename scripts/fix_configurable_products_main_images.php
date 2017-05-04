<?php

require_once 'app/Mage.php';
Varien_Profiler::enable();
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);
Mage::app('admin');
Mage::register('isSecureArea', 1);
Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);

/* @var $configurableProvider Powerbody_Bridge_Model_Provider_Product_Configurable */
$configurableProvider = Mage::getSingleton('bridge/provider_product_configurable');
$configurableCollection = $configurableProvider->getConfigurableProducts()
    ->addAttributeToSelect('image');

foreach ($configurableCollection as $configurableProductModel) {

    if(null === $configurableProductModel->getData('image')
        || $configurableProductModel->getData('image') === 'no_selection'
    ) {
        /* @var $configurableCreator Powerbody_Bridge_Model_Product_Configurable_Creator */
        $configurableCreator = Mage::getSingleton('bridge/product_configurable_creator');
        /* @var $typeConfigurableModel Mage_Catalog_Model_Product_Type_Configurable */
        $typeConfigurableModel = Mage::getModel('catalog/product_type_configurable');
        $childProducts = $typeConfigurableModel->getUsedProducts(null, $configurableProductModel);

        foreach ($childProducts as $childProductModel) {
            /* @var $childProductModel Mage_Catalog_Model_Product */
            $images = [];
            $baseDir = Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();

            foreach ($configurableProductModel->getMediaAttributes() as $imageAttribute) {
                /** @var Mage_Catalog_Model_Resource_Eav_Attribute $imageAttribute */
                $imageAttributeCode = $imageAttribute->getAttributeCode();
                $file = $baseDir . $childProductModel->getData($imageAttributeCode);
                if (file_exists($file)) {
                    if (!isset($images[$file])) {
                        $images[$file] = [];
                    }
                    $images[$file][] = $imageAttributeCode;
                }
            }

            if(false === empty($images)) {
                foreach ($images as $file => $imageAttributeList) {
                    if (!is_file($file)) {
                        continue;
                    }
                    try {
                        $configurableProductModel
                            ->addImageToMediaGallery($file, $imageAttributeList, false, false)
                            ->save();
                    } catch (Exception $e) {
                        Mage::logException($e);
                    }
                }
                break;
            }
        }
    }
}
