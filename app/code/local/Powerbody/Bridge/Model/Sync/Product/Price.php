<?php

/**
 * Class Powerbody_Bridge_Model_Sync_Product_Price
 */
class Powerbody_Bridge_Model_Sync_Product_Price extends Powerbody_Bridge_Model_Sync_Abstract
{
    const IMPORT_PARTITION_SIZE = 500;
    const SERVICE_METHOD = 'getProductsPriceForDropclient';

    /** @var Powerbody_Manufacturer_Helper_Data */
    private $_manufacturerHelper;

    /** @var Powerbody_Manufacturer_Model_Provider_Price */
    private $_priceProvider;

    public function __construct()
    {
        parent::__construct();
        $this->_manufacturerHelper = Mage::helper('manufacturer');
        $this->_priceProvider = Mage::getModel('manufacturer/provider_price');
    }

    public function processImport()
    {
        $skuArray = $this->_getProductProvider()->getImportedProductsToUpdateSkuArray();
        if (empty($skuArray)) {
            return;
        }

        $skuArray = array_chunk($skuArray, self::IMPORT_PARTITION_SIZE);
        $prices = [];

        foreach ($skuArray as $skuPartialArray) {
            $productsPriceArray = $this->_getProductsPriceArray(['sku' => $skuPartialArray]);
            if ($productsPriceArray === null) {
                continue;
            }
            $prices = array_merge($prices, $productsPriceArray);
        }

        if (!empty($prices)) {
            $this->_savePrices($prices);
        }
    }

    /**
     * @param array $sku
     * @return array|null
     */
    protected function _getProductsPriceArray(array $sku)
    {
        return $this->_makeServiceMethodRequest(self::SERVICE_METHOD, ['sku' => $sku]);
    }
    
    /**
     * @param array $productsArray
     */
    protected function _savePrices(array $productsArray)
    {
        $marginArray = $this->_priceProvider->getMarginForProducts();

        foreach ($productsArray as $productArray) {
            if (!isset($productArray['price']) || !isset($productArray['sku'])) {
                continue;
            }

            $productModel = $this->_getProductModel($productArray['sku']);

            if ($productModel->getId() === null
                || $productModel->getData('type_id') === Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
            ) {
                continue;
            }

            $price = $this->_priceProvider->calculateMarginForProduct(
                $productModel,
                $productArray['price'],
                $marginArray
            );
            $price = $this->_manufacturerHelper->convertExternalPriceToLocalPrice($price);
            if ($this->_priceShouldBeUpdated($productModel, $price) === false) {
                continue;
            }
            $productModel->setData('price', $price);
            $productModel->getResource()->saveAttribute($productModel, 'price');
        }

        /* @var $configurableService Powerbody_Bridge_Model_Service_Product_Configurable */
        $configurableService = Mage::getSingleton('bridge/service_product_configurable');
        $configurableService->updateConfigurablePrices();
    }

    /**
     * @param Mage_Catalog_Model_Product $productModel
     * @param float $price
     * @return bool
     */
    private function _priceShouldBeUpdated(Mage_Catalog_Model_Product $productModel, $price)
    {
        return (float) $price !== (float) $productModel->getData('price');
    }
}
