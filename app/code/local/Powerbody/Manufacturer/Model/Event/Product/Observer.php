<?php

/**
 * Class Powerbody_Manufacturer_Model_Event_Product_Observer
 */
class Powerbody_Manufacturer_Model_Event_Product_Observer
{
    public function updateProductMargin()
    {
        /* @var Powerbody_Manufacturer_Helper_Data $helper */
        $helper = Mage::helper('manufacturer');
        $minimalMargin = $helper->getMinimalMargin();

        /* @var Powerbody_Manufacturer_Model_Provider_Manufacturer $manufacturerProvider */
        $manufacturerProvider = Mage::getSingleton('manufacturer/provider_manufacturer');
        $manufacturerCollection = $manufacturerProvider
            ->getManufacturerCollectionWithMarginLowerThan($minimalMargin);

        /* @var Powerbody_Manufacturer_Model_Service_Manufacturer_Margin_Updater $marginUpdater */
        $marginUpdater = Mage::getModel('manufacturer/service_manufacturer_margin_updater');
        $marginUpdater->massUpdateMargin($manufacturerCollection->getAllIds(), $minimalMargin);
    }
    /**
     * Event observer <catalog_product_load_after>
     * @param Varien_Event_Observer $observer
     *
     * @return \Powerbody_Manufacturer_Model_Event_Product_Observer
     */
    public function loadProductManufacturerIds(Varien_Event_Observer $observer)
    {
        try {
            /* @var $catalogProductModel Mage_Catalog_Model_Product */
            $catalogProductModel = $observer->getEvent()->getData('product');
            /* @var Powerbody_Manufacturer_Model_Provider_Manufacturer $manufacturerProvider */
            $manufacturerProvider = Mage::getSingleton('manufacturer/provider_manufacturer');
            $catalogProductModel->setData(
                'manufacturer_ids',
                $manufacturerProvider->getProductManufacturerIds($catalogProductModel->getId())
            );
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }
    
    /**
     * Event observer <catalog_product_save_after>
     * @param Varien_Event_Observer $observer
     *
     * @return \Powerbody_Manufacturer_Model_Event_Product_Observer
     */
    public function saveProductManufacturerIds(Varien_Event_Observer $observer)
    {
        try {
            /* @var $catalogProductModel Mage_Catalog_Model_Product */
            $catalogProductModel = $observer->getEvent()->getData('product');
            /* @var Powerbody_Manufacturer_Model_Service_Product_Manufacturer $productManufacturerService */
            $productManufacturerService = Mage::getSingleton('manufacturer/service_product_manufacturer');
            $productManufacturerService->saveProductManufacturerIds($catalogProductModel);
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }
}
