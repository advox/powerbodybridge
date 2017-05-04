<?php

/**
 * Class Powerbody_Bridge_Model_Sync_Product
 */
class Powerbody_Bridge_Model_Sync_Product extends Powerbody_Bridge_Model_Sync_Product_Abstract
{
    const TYPE_ID_SIMPLE = 1;
    const STOCK_ID = 1;
    const POWERBODY_CO_UK_CODE = 'powerbody_co_uk';
    const GET_PRODUCTS_FOR_DROPCLIENT_METHOD = 'getProductsForDropclient';
    const GET_PRODUCT_SKUS_FOR_DROPCLIENT_METHOD = 'getProductSKUsForDropclient';
    const MANUFACTURER_ARRAY_FIRST_MANUFACTURER = 0;

    /** @var array */
    protected static $_unusedDataFields = [
        'entity_id',
        'stock_item',
        'website_ids',
        'image',
        'small_image',
        'thumbnail',
        'qty',
        'stock_status'
    ];

    /** @var Mage_Core_Model_Store|null */
    private $_activeStoreModel = null;

    /** @var Mage_Eav_Model_Entity_Type|null */
    private $_productEntityType = null;

    /** @var array|null */
    private $_attributeMapped = null;

    /** @var Powerbody_Bridge_Model_Sync_Product_Configurable */
    private $_configurableService;

    /** @var array */
    private static $_attributeCodes = ['size', 'color', 'flavour', 'weight_configurable'];

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->_activeStoreModel = $this->_getStoreModel()
            ->load($this->_getBridgeHelper()->getDefaultDestinationStoreIdConfig());
        $this->_configurableService = Mage::getSingleton('bridge/sync_product_configurable');
    }

    /**
     * process import
     */
    public function processImport()
    {
        $selectedImportedCategoriesBaseIds = $this->_getImportedCategoriesProvider()
            ->getSelectedImportedCategoriesBaseIds();
        $selectedImportedManufacturersBaseIds = $this->_getImportedManufacturersProvider()
            ->getSelectedImportedManufacturersBaseIds();

        $itemsDataArray = $this->_getProductsSkuArray(
            $selectedImportedCategoriesBaseIds,
            $selectedImportedManufacturersBaseIds
        );

        $itemsDataArrayForConfigurable = $this->_configurableService->_getProductsSkuArray(
            $selectedImportedCategoriesBaseIds,
            $selectedImportedManufacturersBaseIds
        );

        if (true === $this->_checkResponseArrayIsValid($itemsDataArray)) {
            $this->_removeNotNeededProducts($itemsDataArray['data']);
        }

        if (true === $this->_checkResponseArrayIsValid($itemsDataArrayForConfigurable)) {
            $this->_configurableService->deleteConfigurableProducts($itemsDataArrayForConfigurable['data']);
        }

        if (true === $this->_checkResponseArrayIsValid($itemsDataArray)) {
            $productsSkuArray = $this->_getProductsSkuToUpdate($itemsDataArray['data']);
            $this->_processImportQueue($productsSkuArray);
        }

        if (true === $this->_checkResponseArrayIsValid($itemsDataArrayForConfigurable)) {
            $this->_configurableService->processImport();
        }

        /* @var $categoryIndexerProductModel Mage_Catalog_Model_Category_Indexer_Product */
        $categoryIndexerProductModel = Mage::getModel('catalog/category_indexer_product');
        $categoryIndexerProductModel->reindexAll();
    }

    /**
     * reload additional product data
     */
    public function getProductsImportDataForCurrentProducts()
    {
        /* @var $catalogSimpleProductCollection Mage_Catalog_Model_Resource_Product_Collection */
        $catalogSimpleProductCollection = Mage::getModel('catalog/product')->getCollection()
            ->addFieldToFilter('type_id', Mage_Catalog_Model_Product_Type::TYPE_SIMPLE);

        $productsSkuArray = $catalogSimpleProductCollection->getColumnValues('sku');

        return $this->_getProductsData($productsSkuArray);
    }

    /**
     * @param array $itemsDataArray
     */
    private function _removeNotNeededProducts(array $itemsDataArray)
    {
        $skuArray = array_keys($itemsDataArray);
        $productsIdsWithoutMatchingSKU = $this->_getProductsIdsWithoutMatchingSKU($skuArray);
        if (true === empty($productsIdsWithoutMatchingSKU)) {
            return;
        }

        $this->_getProductRemoveService()->removeProducts($productsIdsWithoutMatchingSKU);
    }

    /**
     * @param array $skuArray
     *
     * @return array
     */
    private function _getProductsIdsWithoutMatchingSKU(array $skuArray)
    {
        return $this->_getProductProvider()->getProductsIdsWithoutMatchingSKU($skuArray);
    }

    /**
     * @param array $itemsDataArray
     *
     * @return array
     */
    private function _getProductsSkuToUpdate(array $itemsDataArray)
    {
        if (true === empty($itemsDataArray)) {
            return [];
        }

        $productsSkuArray = [];
        foreach ($itemsDataArray as $sku => $updatedAt) {
            $productModel = $this->_getProductModel($sku);
            if (null === $productModel->getId()) {
                $productsSkuArray[] = $sku;
                continue;
            }

            $productUpdatedAt = $productModel->getData('updated_at');
            $internalUpdatedAt = new DateTime($productUpdatedAt);
            $externalUpdatedAt = new DateTime($updatedAt);
            if (null === $productUpdatedAt || $internalUpdatedAt < $externalUpdatedAt) {
                $productsSkuArray[] = $sku;
            }
        }

        return $productsSkuArray;
    }

    /**
     * @param array $itemsDataArray
     */
    protected function _saveItems(array $itemsDataArray)
    {
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        foreach ($itemsDataArray as $itemData) {
            $this->_saveItem($itemData);
        }
    }

    /**
     * @param array $itemData
     */
    private function _saveItem(array $itemData)
    {
        try {
            $this->_saveSimpleProduct($itemData);
        } catch (Exception $e) {
            $this->_getLog()->logException($e);
        }
    }

    /**
     * @param array $itemsDataArray
     */
    protected function _afterSave(array $itemsDataArray)
    {
        $manufacturersIds = [];
        foreach ($itemsDataArray as $itemData) {
            if (isset($itemData['manufacturer_id'])) {
                $manufacturersIds = array_merge($manufacturersIds, $itemData['manufacturer_id']);
            }
            if (isset($itemData['manufacturers'])) {
                $manufacturersIds = array_merge($manufacturersIds, $itemData['manufacturers']);
            }
        }
        $manufacturersIds = array_unique($manufacturersIds);
        foreach ($manufacturersIds as $manufacturerId) {
            $manufacturerModel = Mage::getModel('manufacturer/manufacturer')->load($manufacturerId);
            if ($manufacturerModel->getId() !== null) {
                $manufacturerModel->setData('store_ids', [$this->_activeStoreModel->getId()]);
                $manufacturerModel->setDataChanges(true);
                $manufacturerModel->save();
            }
        }
    }

    /**
     * @param array $itemData
     */
    private function _saveSimpleProduct(array $itemData)
    {
        $itemData = $this->_translateIds($itemData);
        $attributesMapped = $this->_processAttributeWithSource($itemData['attributes_source']);

        $stockData = [
            'is_in_stock'   => $itemData['stock_status'],
            'qty'           => $itemData['qty'],
        ];
        $itemData = $this->_removeNotUsedDataFields($itemData);

        /* @var $productModel Mage_Catalog_Model_Product */
        $productModel = $this->_getProductModel($itemData['sku']);

        $isProductNew = ($productModel->getId() === null);

        $itemData = $this->_removeNotNeededDataFromImport($productModel, $itemData, $isProductNew);
        if ($isProductNew === false) {
            unset($itemData['price']);
        }

        $productModel
            ->addData($itemData)
            ->addData($attributesMapped)
            ->setData('is_imported', true)
            ->setData('website_ids', $this->_getWebsiteIds())
            ->setData('status', (int) $itemData['status']);

        if (true === $isProductNew) {
            $productModel->setData('is_updated_while_import', true);
        }

        if (isset($itemData['manufacturers']) && false === empty($itemData['manufacturers'])) {
            $productModel->setData('manufacturer_ids', $itemData['manufacturers']);
            $productModel->setData('manufacturer', $itemData['manufacturers'][self::MANUFACTURER_ARRAY_FIRST_MANUFACTURER]);
        }

        $productModel->save();

        if (true === isset($itemData['image_url']) && false === empty($itemData['image_url'])) {
            $this->_getImageSync()->addProductImages($productModel, $itemData['image_url']);
        }

        /* @var $stockItemModel Mage_CatalogInventory_Model_Stock_Item */
        $stockItemModel = $this->_getStockItemByProductId($productModel->getId());
        $stockItemModel
            ->setProduct($productModel)
            ->setData('stock_id', self::STOCK_ID)
            ->setData('qty', $stockData['qty'])
            ->setData('is_in_stock', $stockData['is_in_stock'])
            ->save();
    }

    /**
     * @param array $attributes
     *
     * @return array
     */
    private function _processAttributeWithSource(array $attributes)
    {
        $attributeMapped = $this->getAttributesForMapping();
        if (empty($attributeMapped)) {
            return [];
        }

        $entityTypeId = $this->_getProductEntityType()->getId();
        $productAttributes = [];
        foreach ($attributes as $attributeCode => $attributeValue) {

            if (isset($attributeMapped[$entityTypeId][$attributeCode][$attributeValue])) {
                $productAttributes[$attributeCode] = $attributeMapped[$entityTypeId][$attributeCode][$attributeValue];
            }

            $attributeValue = trim($attributeValue);

            if (isset($attributeMapped[$entityTypeId][$attributeCode][$attributeValue])) {
                $productAttributes[$attributeCode] = $attributeMapped[$entityTypeId][$attributeCode][$attributeValue];
            }
        }

        return $productAttributes;
    }

    /**
     * @param Mage_Catalog_Model_Product $productModel
     * @param array $itemData
     * @param bool $isProductNew
     *
     * @return array
     */
    private function _removeNotNeededDataFromImport(
        Mage_Catalog_Model_Product $productModel,
        array $itemData,
        $isProductNew
    ) {
        if (false === $isProductNew
            && false === boolval($productModel->getData('is_updated_while_import'))
        ) {
            unset(
                $itemData['name'],
                $itemData['meta_title'],
                $itemData['meta_description'],
                $itemData['price'],
                $itemData['description'],
                $itemData['short_description'],
                $itemData['image_url']
            );
        }

        return $itemData;
    }

    /**
     * @param array $itemData
     *
     * @return array
     */
    private function _translateIds(array $itemData)
    {
        $productEntityType = $this->_getProductEntityType();
        $itemData['entity_type_id'] = $productEntityType->getId();
        $itemData['attribute_set_id'] = $productEntityType->getDefaultAttributeSetId();
        if (isset($itemData['category_ids']) && is_array($itemData['category_ids'])) {
            $itemData['category_ids'] = $this->_getInternalCategoryIds($itemData['category_ids']);
        }
        if (isset($itemData['manufacturers']) && is_array($itemData['manufacturers'])) {
            $itemData['manufacturers'] = $this->_getInternalManufacturerIds($itemData['manufacturers']);
        }

        return $itemData;
    }

    /**
     * @return Mage_Eav_Model_Entity_Type
     */
    private function _getProductEntityType()
    {
        if (!$this->_productEntityType instanceof Mage_Eav_Model_Entity_Type) {
            $this->_productEntityType = $this->_getEavConfig()->getEntityType(Mage_Catalog_Model_Product::ENTITY);
        }

        return $this->_productEntityType;
    }

    /**
     * @return array|null
     */
    private function getAttributesForMapping()
    {
        if ($this->_attributeMapped === null) {
            /* @var $syncAttributeModel Powerbody_Bridge_Model_Sync_Attribute */
            $syncAttributeModel = Mage::getSingleton('bridge/sync_attribute');

            $this->_attributeMapped = $syncAttributeModel->getAttributesForMapping(
                $this->_getProductEntityType()->getId(),
                self::$_attributeCodes
            );
        }

        return $this->_attributeMapped;
    }

    /**
     * @param array $productsSkuArray
     *
     * @return array|null
     */
    protected function _getProductsData(array $productsSkuArray)
    {
        return $this->_makeServiceMethodRequest(
            self::GET_PRODUCTS_FOR_DROPCLIENT_METHOD,
            [
                'type_id'       => self::TYPE_ID_SIMPLE,
                'code'          => self::POWERBODY_CO_UK_CODE,
                'product_sku'   => implode(',', $productsSkuArray),
            ]
        );
    }
}
