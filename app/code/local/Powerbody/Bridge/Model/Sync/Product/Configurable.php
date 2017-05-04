<?php

/**
 * Class Powerbody_Bridge_Model_Sync_Product_Configurable
 */
class Powerbody_Bridge_Model_Sync_Product_Configurable extends Powerbody_Bridge_Model_Sync_Product_Abstract
{
    const GET_CONFIGURABLE_PRODUCT_LIST_METHOD = 'getConfigurableProductList';
    const GET_PRODUCT_SKUS_FOR_DROPCLIENT_METHOD = 'getConfigurableProductsSku';

    /** @var array */
    protected static $_unusedDataFields = ['store_id', 'entity_id'];

    /** @var Powerbody_Bridge_Model_Product_Configurable_Creator */
    private $_creator;

    /** @var array */
    private $_compareData = [];

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->_creator = Mage::getModel('bridge/product_configurable_creator');
    }

    /**
     * process import
     */
    public function processImport()
    {
        $productUpdateArray = $this->_addProductsToProductUpdateArray($this->_compareData);
        $this->_processImportQueue($productUpdateArray);
    }

    /**
     * @param array $itemsDataArray
     */
    public function deleteConfigurableProducts(array $itemsDataArray)
    {
        $this->_compareResponseDataToDropclientData($itemsDataArray);
        $this->_deleteUnusedConfigurableProducts($this->_compareData);
    }

    /**
     * @param array $itemsDataArray
     */
    protected function _afterSave(array $itemsDataArray)
    {
        return;
    }

    /**
     * @param array $itemsDataArray
     */
    protected function _saveItems(array $itemsDataArray)
    {
        $configurableProductToDelete = [];
        $configurableDataToCreate = [];

        foreach ($itemsDataArray as $itemData) {
            $itemData['website_ids'] = $this->_getWebsiteIds();

            if (!isset($itemData['entity_id'])) {
                continue;
            }

            /* @var $configurableProductModel Mage_Catalog_Model_Product */
            $configurableProductModel = $this->_getProductModel($itemData['sku']);

            $itemData = $this->_removeNotUsedDataFields($itemData);

            if (null === $configurableProductModel->getId()) {
                $this->_createProduct($itemData);
            } else {
                $result = $this->_compareCurrentProductWithWebserviceProduct($configurableProductModel, $itemData);

                if (true === $result['create']) {
                    $configurableProductToDelete['delete'][] = $itemData['sku'];
                    $configurableDataToCreate[] = $itemData;
                }

                if (true === $result['update']) {
                    $this->_updateConfigurableProduct($configurableProductModel, $itemData);
                }
            }
        }

        $this->_deleteUnusedConfigurableProducts($configurableProductToDelete);

        foreach ($configurableDataToCreate as $itemData) {
            $this->_createProduct($itemData);
        }
    }

    /**
     * @param array $compareData
     *
     * @return array
     */
    protected function _addProductsToProductUpdateArray(array $compareData)
    {
        return array_merge($compareData['create'], $compareData['update']);
    }

    /**
     * @param array $compareData
     */
    protected function _deleteUnusedConfigurableProducts(array $compareData)
    {
        if (true === isset($compareData['delete']) && false === empty($compareData['delete'])) {
            $productsIdsFromSKU = $this->_getProductsIdsFromSKUArray($compareData['delete']);

            if (true === empty($productsIdsFromSKU)) {
                return;
            }

            $this->_getProductRemoveService()->removeProducts($productsIdsFromSKU);
        }
    }

    /**
     * @param array $itemsDataArray
     */
    protected function _compareResponseDataToDropclientData(array $itemsDataArray)
    {
        $dropclientConfigurableArray = $this->_getCurrentDropclientConfigurable();

        $this->_compareData = [
            'create' => $this->_compareConfigurableToCreate($dropclientConfigurableArray, $itemsDataArray),
            'delete' => $this->_compareConfigurableToDelete($dropclientConfigurableArray, $itemsDataArray),
            'update' => $this->_compareConfigurableToUpdate($dropclientConfigurableArray, $itemsDataArray),
        ];
    }

    /**
     * @param array $dropclientArray
     * @param array $webserviceArray
     *
     * @return array
     */
    protected function _compareConfigurableToUpdate(array $dropclientArray, array $webserviceArray)
    {
        $configurableSKUsToUpdateArray = [];

        foreach ($webserviceArray as $webserviceSku => $webserviceData) {
            if (true === isset($dropclientArray[$webserviceSku])) {
                $webserviceUpdatedAt = new DateTime($webserviceArray[$webserviceSku]['updated_at']);
                $dropclientUpdatedAt = new DateTime($dropclientArray[$webserviceSku]['updated_at']);

                $productUpdatedAt = $dropclientArray[$webserviceSku]['updated_at'];

                if (null === $productUpdatedAt || $webserviceUpdatedAt > $dropclientUpdatedAt) {
                    $configurableSKUsToUpdateArray[] = $webserviceSku;
                }
            }
        }

        return $configurableSKUsToUpdateArray;
    }
    
    /**
     * @param array $dropclientArray
     * @param array $webserviceArray
     *
     * @return array
     */
    protected function _compareConfigurableToDelete(array $dropclientArray, array $webserviceArray)
    {
        $tmpProductsToDelete = array_diff_key($dropclientArray, $webserviceArray);

        return array_keys($tmpProductsToDelete);
    }

    /**
     * @param array $dropclientArray
     * @param array $webserviceArray
     *
     * @return array
     */
    protected function _compareConfigurableToCreate(array $dropclientArray, array $webserviceArray)
    {
        $tmpProductsToCreate = array_diff_key($webserviceArray, $dropclientArray);

        return array_keys($tmpProductsToCreate);
    }
    
    /**
     * @return array
     */
    protected function _getCurrentDropclientConfigurable()
    {
        $productConfigurableArray = [];

        /* @var $catalogProductCollection Mage_Catalog_Model_Resource_Product_Collection */
        $catalogProductCollection = Mage::getModel('catalog/product')->getCollection();
        $catalogProductCollection->addAttributeToFilter(
            'type_id',
            Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
        );

        /* @var $catalogProductModel Mage_Catalog_Model_Product */
        foreach ($catalogProductCollection as $catalogProductModel) {
            $productConfigurableArray[$catalogProductModel->getData('sku')]['updated_at']
                = $catalogProductModel->getData('updated_at');
        }

        return $productConfigurableArray;
    }

    /**
     * @param array $productsSkuArray
     *
     * @return array|null
     */
    protected function _getProductsData(array $productsSkuArray)
    {
        $configurableProductsData = [];
        if (false === empty($productsSkuArray)) {
            $serviceParams['product_sku'] = $productsSkuArray;
            $configurableProductsData = $this->_makeServiceMethodRequest(
                self::GET_CONFIGURABLE_PRODUCT_LIST_METHOD,
                $serviceParams
            );
        }

        return $configurableProductsData;
    }

    /**
     * @param Mage_Catalog_Model_Product $configurableProductModel
     * @param array                      $productData
     */
    protected function _updateConfigurableProduct(
        Mage_Catalog_Model_Product $configurableProductModel,
        array $productData
    )
    {
        try {
            $shortData = $this->_generateShortProductArray($productData);

            $configurableProductModel->addData($shortData);
            if (true === isset($productData['manufacturers'])) {
                $configurableProductModel->setManufacturers(
                    $this->_getInternalManufacturerIds($productData['manufacturers'])
                );
                $configurableProductModel->setData(
                    'manufacturer_ids', $this->_getInternalManufacturerIds($productData['manufacturers'])
                );
            }
            if (true === isset($productData['categories']) && false === empty($productData['categories'])) {
                $configurableProductModel->setCategoryIds(
                    $this->_getInternalCategoryIds($productData['categories'])
                );
            }
            $configurableProductModel->save();

            /* @var $stockItemModel Mage_CatalogInventory_Model_Stock_Item */
            $stockItemModel = $this->_getStockItemByProductId($configurableProductModel->getId());
            $stockItemModel
                ->setData('is_in_stock', $shortData['stock_availability'])
                ->save();
        } catch (Exception $e) {
            $this->_getLog()->logException($e);
        }
    }

    /**
     * @param Mage_Catalog_Model_Product $catalogProductModel
     * @param array                      $webserviceData
     *
     * @return array
     */
    protected function _compareCurrentProductWithWebserviceProduct(
        Mage_Catalog_Model_Product $catalogProductModel,
        array $webserviceData
    )
    {
        $webserviceChildrenArray = array_keys($webserviceData['children']);
        $configurableProductChildrenArray = $this->_getConfigurableProductChildrenArray($catalogProductModel);

        if (true === empty(array_diff($configurableProductChildrenArray, $webserviceChildrenArray))
            && true === empty(array_diff($webserviceChildrenArray, $configurableProductChildrenArray))
        ) {
            return [
                'update' => $this->_isProductToUpdate($catalogProductModel, $webserviceData),
                'create' => false,
            ];
        }

        return ['create' => true, 'update' => false];
    }

    /**
     * @param Mage_Catalog_Model_Product $catalogProductModel
     * @param array                      $webserviceData
     *
     * @return bool
     */
    protected function _isProductToUpdate(
        Mage_Catalog_Model_Product $catalogProductModel,
        array $webserviceData
    ) {
        $webserviceArray = $this->_generateShortProductArray($webserviceData);
        $catalogProductArray = $this->_generateShortProductArray($catalogProductModel);

        return $catalogProductArray !== $webserviceArray;
    }

    /**
     * @param array|Mage_Catalog_Model_Product $productData
     *
     * @return array
     */
    protected function _generateShortProductArray($productData)
    {
        if (true === $productData instanceof Mage_Catalog_Model_Product) {
            return [
                'name'               =>  $productData->getData('name'),
                'description'        =>  $productData->getData('description'),
                'short_description'  =>  $productData->getData('short_description'),
                'status'             =>  (int)$productData->getData('status'),
                'tax_class_id'       =>  (int)$productData->getData('tax_class_id'),
                'stock_availability' =>  (int)$productData->isInStock(),
            ];
        }

        if (true === is_array($productData)) {
            $description = '';
            if (true === isset($productData['description'])) {
                $description = $productData['description'];
            }
            $shortDescription = '';
            if (true === isset($productData['short_description'])) {
                $shortDescription = $productData['short_description'];
            }

            return [
                'name'               =>  $productData['name'],
                'description'        =>  $description,
                'short_description'  =>  $shortDescription,
                'status'             =>  (int)$productData['status'],
                'tax_class_id'       =>  (int)$productData['tax_class_id'],
                'stock_availability' =>  (int)$productData['stock_availability'],
            ];
        }

        return [];
    }

    /**
     * @param array $productData
     */
    protected function _createProduct(array $productData)
    {
        $this->_connection->beginTransaction();
        try {
            $this->_creator->createConfigurableProduct($productData);
            $this->_connection->commit();
        } catch (Exception $e) {
            $this->_getLog()->logException($e);
            $this->_connection->rollBack();
        }
    }

    /**
     * @param array $skuArray
     *
     * @return array
     */
    private function _getProductsIdsFromSKUArray(array $skuArray)
    {
        return $this->_getProductProvider()->getProductsIdsFromSKUArray($skuArray);
    }

    /**
     * @param Mage_Catalog_Model_Product $catalogProductModel
     *
     * @return array
     */
    private function _getConfigurableProductChildrenArray(Mage_Catalog_Model_Product $catalogProductModel)
    {
        /* @var $productTypeConfigurableModel Mage_Catalog_Model_Product_Type_Configurable */
        $productTypeConfigurableModel = Mage::getModel('catalog/product_type_configurable');
        $childProducts = $productTypeConfigurableModel->getUsedProducts(null, $catalogProductModel);

        $configurableProductChildrenArray = [];
        foreach ($childProducts as $child) {
            $configurableProductChildrenArray[] = $child->getData('sku');
        }

        return $configurableProductChildrenArray;
    }
}
