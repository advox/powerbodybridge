<?php

/**
 * Class Powerbody_Manufacturer_Model_Service_Product_Price_Updater
 */
class Powerbody_Manufacturer_Model_Service_Product_Price_Updater
{
    /**
     * @var Powerbody_Manufacturer_Model_Provider_Product
     */
    private $_manufacturerProductProvider;

    /**
     * @var Powerbody_Manufacturer_Model_Provider_Price
     */
    private $_manufacturerPriceProvider;

    /**
     * @var Mage_Core_Model_Resource
     */
    private $_coreResource;

    /**
     * @var Varien_Db_Adapter_Interface
     */
    private $_DAO;

    /**
     * @var Powerbody_Manufacturer_Model_Provider_Catalog_Product_Superattribute
     */
    private $_superAttributeProvider;

    /**
     * @var Powerbody_Manufacturer_Model_Provider_Catalog_Product_Price
     */
    private $_productPriceProvider;

    public function __construct()
    {
        $this->_manufacturerProductProvider = Mage::getModel('manufacturer/provider_product');
        $this->_manufacturerPriceProvider = Mage::getModel('manufacturer/provider_price');
        $this->_coreResource = Mage::getModel('core/resource');
        $this->_DAO = $this->_coreResource->getConnection('core_write');
        $this->_superAttributeProvider = Mage::getSingleton('manufacturer/provider_catalog_product_superattribute');
        $this->_productPriceProvider = Mage::getSingleton('manufacturer/provider_catalog_product_price');
    }

    /**
     * @param Powerbody_Manufacturer_Model_Manufacturer $manufacturerModel
     */
    public function updateProductsPrices(Powerbody_Manufacturer_Model_Manufacturer $manufacturerModel)
    {
        $oldMargin = (int) $manufacturerModel->getOrigData('margin');
        $newMargin = (int) $manufacturerModel->getData('margin');
        if ($oldMargin === $newMargin) {
            return;
        }

        $productCollection = $this->_getManufacturerProductCollectionWithPrices($manufacturerModel);

        $marginArray = $this->_manufacturerPriceProvider->getMarginForProducts();
        foreach ($productCollection as $productModel) {
            $this->_adjustProductPriceMargin($productModel, $oldMargin, $marginArray);
        }

        /* @var $configurableService Powerbody_Bridge_Model_Service_Product_Configurable */
        $configurableService = Mage::getSingleton('bridge/service_product_configurable');
        $configurableService->updateConfigurablePrices();
    }

    /**
     * @param Powerbody_Manufacturer_Model_Manufacturer $manufacturerModel
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    private function _getManufacturerProductCollectionWithPrices(Powerbody_Manufacturer_Model_Manufacturer $manufacturerModel)
    {
        return $this->_manufacturerProductProvider
            ->getProductCollection($manufacturerModel->getId())
            ->addAttributeToSelect('price');
    }

    /**
     * @param Mage_Catalog_Model_Product $productModel
     * @param $currentMargin
     * @param array $marginArray
     */
    private function _adjustProductPriceMargin(Mage_Catalog_Model_Product $productModel, $currentMargin, array $marginArray)
    {
        $basePrice = $productModel->getPrice() / (1 + ($currentMargin / 100));
        $price = $this->_manufacturerPriceProvider->calculateMarginForProduct($productModel, $basePrice, $marginArray);
        $productModel->setData('price', $price);
        $productModel->getResource()->saveAttribute($productModel, 'price');
    }
}
