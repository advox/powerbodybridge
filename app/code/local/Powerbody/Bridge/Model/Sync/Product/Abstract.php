<?php

/**
 * Class Powerbody_Bridge_Model_Sync_Abstract
 */
abstract class Powerbody_Bridge_Model_Sync_Product_Abstract extends Powerbody_Bridge_Model_Sync_Abstract
{
    const IMPORT_PACKAGE_SIZE = 300;

    /**
     * @var array
     */
    protected static $_unusedDataFields = [];

    /**
     * @var array|null
     */
    protected $_websiteIds = null;

    /**
     * @param array $productsSkuArray
     * @return array
     */
    abstract protected function _getProductsData(array $productsSkuArray);

    /**
     * @param array $itemsDataArray
     */
    abstract protected function _saveItems(array $itemsDataArray);

    /**
     * @param array $itemsDataArray
     */
    abstract protected function _afterSave(array $itemsDataArray);

    /**
     * @return array|null
     * @throws \Exception
     */
    final protected function _getProductsSkuArray($selectedImportedCategoriesBaseIds, $selectedImportedManufacturersBaseIds)
    {
        if (empty($selectedImportedCategoriesBaseIds) || empty($selectedImportedManufacturersBaseIds)) {
            return ['success' => true, 'data' => []];
        }

        $productsSkuServiceParams = [
            'categories_ids'    => $selectedImportedCategoriesBaseIds,
            'manufacturers_ids' => $selectedImportedManufacturersBaseIds,
        ];

        return $this->_makeServiceMethodRequest(
            static::GET_PRODUCT_SKUS_FOR_DROPCLIENT_METHOD,
            $productsSkuServiceParams
        );
    }

    /**
     * @param array $productsSkuArray
     */
    protected function _processImportQueue(array $productsSkuArray)
    {
        if (true === empty($productsSkuArray)) {
            return;
        }

        $productsSkuArrayChunk = array_chunk($productsSkuArray, self::IMPORT_PACKAGE_SIZE);
        foreach ($productsSkuArrayChunk as $productsSkuArray) {
            $itemsDataArray = $this->_getProductsData($productsSkuArray);
            if (true === $this->_checkResponseArrayIsValid($itemsDataArray)) {
                $this->_saveItems($itemsDataArray['data']);
                $this->_afterSave($itemsDataArray['data']);
            }
        }
    }

    /**
     * @param array $itemsDataArray
     *
     * @return bool
     */
    protected function _checkResponseArrayIsValid(array $itemsDataArray)
    {
        return (true === is_array($itemsDataArray)
            && true === isset($itemsDataArray['success'])
            && true === $itemsDataArray['success']
            && true === isset($itemsDataArray['data'])
            && true === is_array($itemsDataArray['data'])
        );
    }

    /**
     * @param array $externalIds
     *
     * @return array
     */
    final protected function _getInternalCategoryIds(array $externalIds)
    {
        /** @var Mage_Catalog_Model_Resource_Category_Collection $categoryCollection */
        $categoryCollection = Mage::getModel('catalog/category')->getCollection();
        $categoryCollection->getSelect()
            ->join(
                $categoryCollection->getResource()->getTable('bridge/imported_category'),
                'client_category_id=entity_id',
                'base_category_id'
            )
            ->where('base_category_id in (?)', $externalIds);

        return $categoryCollection->getColumnValues('entity_id');
    }

    /**
     * @param array $externalIds
     *
     * @return array
     */
    final protected function _getInternalManufacturerIds(array $externalIds)
    {
        /** @var Powerbody_Manufacturer_Model_Mysql4_Manufacturer_Collection $manufacturerCollection */
        $manufacturerCollection = Mage::getModel('manufacturer/manufacturer')->getCollection();
        $manufacturerCollection->getSelect()
            ->join(
                ['bim' => $manufacturerCollection->getResource()->getTable('bridge/imported_manufacturer')],
                'bim.client_manufacturer_id=main_table.id',
                'base_manufacturer_id'
            )
            ->where('base_manufacturer_id in (?)', $externalIds);

        return $manufacturerCollection->getColumnValues('id');
    }

    /**
     * @return array
     */
    final protected function _getWebsiteIds()
    {
        if ($this->_websiteIds === null) {
            $storeModel = $this->_getStoreModel()
                ->load($this->_getBridgeHelper()->getDefaultDestinationStoreIdConfig());
            $this->_websiteIds = [$storeModel->getWebsiteId()];
        }

        return $this->_websiteIds;
    }

    /**
     * @param array $itemData
     * @return array
     */
    final protected function _removeNotUsedDataFields(array $itemData)
    {
        foreach (self::$_unusedDataFields as $toRemove) {
            unset($itemData[$toRemove]);
        }

        return $itemData;
    }

    /**
     * @return Powerbody_Bridge_Model_Provider_Imported_Manufacturers
     */
    final protected function _getImportedManufacturersProvider()
    {
        return Mage::getSingleton('bridge/provider_imported_manufacturers');
    }

    /**
     * @return Powerbody_Bridge_Model_Provider_Imported_Categories
     */
    final protected function _getImportedCategoriesProvider()
    {
        return Mage::getSingleton('bridge/provider_imported_categories');
    }

    /**
     * @return Powerbody_Bridge_Model_Service_Product_Remove
     */
    final protected function _getProductRemoveService()
    {
        return Mage::getModel('bridge/service_product_remove');
    }

    /**
     * @return Powerbody_Bridge_Model_Sync_Product_Images
     */
    final protected function _getImageSync()
    {
        return Mage::getModel('bridge/sync_product_images');
    }

    /**
     * @param string|int $productId
     * @return false|Mage_CatalogInventory_Model_Stock_Item
     */
    final protected function _getStockItemByProductId($productId)
    {
        /** @var Mage_CatalogInventory_Model_Stock_Item $stockItem */
        $stockItem = Mage::getModel('cataloginventory/stock_item');
        $stockItem->loadByProduct($productId);

        return $stockItem;
    }
}
