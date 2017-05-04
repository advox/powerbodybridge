<?php

/**
 * Class Powerbody_Bridge_Model_Sync_Category
 */
class Powerbody_Bridge_Model_Sync_Category
{
    const DEFAULT_LANGUAGE_STORE_CODE = 'powerbody_co_uk';
    const ROOT_CATALOG_CATEGORY_ID = 1;
    const DEFAULT_WEBSITE_ID = 1;

    /* @var string */
    protected $_idField = 'entity_id';

    /* @var null */
    protected $_rootCategoryId = null;

    /* @var string */
    protected $_serviceMethod = 'getAllCategories';

    /* @var Powerbody_Bridge_Model_Service|null */
    protected $_service = null;

    /* @var array */
    protected $_serviceParams = [];

    /* @var array */
    protected $_unusedDataFields = [];

    /**
     * construct
     */
    public function __construct()
    {
        $this->_service = Mage::getSingleton('bridge/service');
        $this->_serviceParams = array(
            'code' => self::DEFAULT_LANGUAGE_STORE_CODE
        );
        $this->_unusedDataFields = array('store_id', 'entity_id');
    }

    /**
     * @param array $categoriesIds
     */
    public function processCatalogCategoriesByIds(array $categoriesIds)
    {
        $this->_getService()->connect();
        /* @var $syncModel Powerbody_Bridge_Model_Sync_Category */
        $this->setServiceParams([
            'categories' => $categoriesIds,
            'locale'     => Mage::app()->getLocale()->getLocaleCode(),
        ]);
        $this->processImport();
        $this->_getService()->disconnect();
    }

    /**
     * Process import items.
     */
    public function processImport()
    {
        $itemsDataArray = $this->_getItemsDataArray();
        if (true === empty($itemsDataArray)) {
            return;
        }
        $this->saveItems($itemsDataArray);
    }

    /**
     * @param int|null $catalogCategoryId
     * @return Mage_Catalog_Model_Category
     */
    protected function _getCatalogCategoryModel($catalogCategoryId = null)
    {
        $catalogCategoryModel = Mage::getModel('catalog/category');
        /* @var $catalogCategoryModel Mage_Catalog_Model_Category */
        if ($catalogCategoryId) {
            $catalogCategoryModel->load($catalogCategoryId);
        }
        return $catalogCategoryModel;
    }

    /**
     * @return int
     */
    protected function _getDefaultEntityTypeId()
    {
        $entityType = Mage::getModel('eav/config')->getEntityType('catalog_category');

        return $entityType->getEntityTypeId();
    }

    /**
     * @return int
     */
    protected function _getRootCategoryId()
    {
        if (!$this->_rootCategoryId) {
            $this->_rootCategoryId = $this->_getRootCatalogCategory();
        }

        return $this->_rootCategoryId;
    }

    /**
     * @return int
     */
    protected function _getRootCatalogCategory()
    {
        $catalogProductModel = Mage::getModel('catalog/category')
        /* @var $catalogProductModel Mage_Catalog_Model_Product */
            ->load(Mage_Catalog_Model_Category::TREE_ROOT_ID);
        if (!$catalogProductModel->getId()) {
            $catalogProductModel->setData(array(
                'attribute_set_id'  => 0,
                'entity_id'         => Mage_Catalog_Model_Category::TREE_ROOT_ID,
                'entity_type_id'    => $this->_getDefaultEntityTypeId(),
                'level'             => 0,
                'parent_id'         => 0,
                'path'              => Mage_Catalog_Model_Category::TREE_ROOT_ID,
                'position'          => Mage_Catalog_Model_Category::TREE_ROOT_ID,
            ));
            try {
                $catalogProductModel->save();
            } catch (Exception $e) {
                Mage::log($e->getMessage());
                Mage::logException($e);
            }
        }

        return $catalogProductModel->getId();
    }

    /**
     * @param array $itemsDataArray
     */
    public function saveItems(array $itemsDataArray)
    {
        foreach ($itemsDataArray as $itemData) {
            if (false === isset($itemData[$this->_idField])) {
                continue;
            }
            $baseId = $itemData[$this->_idField];
            $itemData = $this->_removeNotUsedDataFields($itemData);
            $id = $this->_getCategoryNewId($baseId);
            $modelId = $this->_saveItem($id, $itemData);
            $this->_assignCatalogCategoryToImportedCategory($baseId, $modelId);
        }
    }

    /**
     * @param int $baseId
     * @return string|null
     */
    protected function _getCategoryNewId($baseId)
    {
        if (null !== $baseId) {
            /* @var $importedCategoryModel Powerbody_Bridge_Model_Imported_Category */
            $importedCategoryModel = Mage::getModel('bridge/imported_category')->load($baseId, 'base_category_id');
            return $importedCategoryModel->getData('client_category_id');
        }
        return null;
    }

    /**
     * @param int $baseId
     * @param int $categoryId
     */
    protected function _assignCatalogCategoryToImportedCategory($baseId, $categoryId)
    {
        /* @var $importedCategoryModel Powerbody_Bridge_Model_Imported_Category */
        $importedCategoryModel = Mage::getModel('bridge/imported_category')->load($baseId, 'base_category_id');
        $importedCategoryModel->setData('client_category_id', $categoryId);
        $importedCategoryModel->save();
    }

    /**
     * @param string $parentId
     * @return mixed
     */
    protected function _getParentCatalogCategoryId($parentId)
    {
        /* @var $importedCategoryModel Powerbody_Bridge_Model_Imported_Category */
        $importedCategoryModel = Mage::getModel('bridge/imported_category')->load($parentId, 'base_category_id');
        return $importedCategoryModel->getData('client_category_id');
    }

    /**
     * Save new/existing category.
     * @param int $id
     * @param array $itemData
     *
     * @return int|null
     */
    protected function _saveItem($id, $itemData)
    {
        try {
            $catalogCategoryModel = $this->_getCatalogCategoryModel($id);
            /* @var $catalogCategoryModel Mage_Catalog_Model_Category */
            $rootCategoryId = $this->_getRootCategoryId();
            /* @var $parentCatalogCategoryModel Mage_Catalog_Model_Category */
            $parentCatalogCategoryId = $this->_getParentCatalogCategoryId($itemData['parent_id']);
            if (null === $parentCatalogCategoryId) {
                $parentCatalogCategoryId = self::ROOT_CATALOG_CATEGORY_ID;
            }
            $parentCatalogCategoryModel = Mage::getModel('catalog/category')->load($parentCatalogCategoryId);
            if (isset($itemData['children_count'])) {
                $itemData['children_count'] = 0;
            }
            if (true === isset($itemData['parent_id'])) {
                $itemData['parent_id'] = $this->_setCatalogCategoryParentId(
                    $parentCatalogCategoryModel,
                    $rootCategoryId
                );
            }
            if (isset($itemData['path'])) {
                $itemData['path'] = $this->_setCatalogCategoryPath(
                    $id,
                    $parentCatalogCategoryModel,
                    $rootCategoryId,
                    $catalogCategoryModel
                );
            }
            $catalogCategoryModel->addData($itemData);
            $catalogCategoryModel->save();
            $catalogCategoryId = $catalogCategoryModel->getId();

            if ($itemData['parent_id'] === $this->_getRootCategoryId()) {
                $this->_assignAsDefaultCategory($catalogCategoryId);
            }

            return $catalogCategoryId;
        } catch (Exception $e) {
            Mage::log($e->getMessage());
            Mage::logException($e);
        }
        return null;
    }

    /**
     * @param array $params
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setServiceParams(array $params)
    {
        $this->_serviceParams = $params;

        return $this;
    }

    /**
     * @return Powerbody_Bridge_Model_Service
     */
    protected function _getService()
    {
        return $this->_service;
    }

    /**
     * @return array
     *
     * @throws Exception
     */
    protected function _getItemsDataArray()
    {
        $dataArray = [];
        $this->_getService()->connect();

        try {
            $dataArray = $this->_getService()->callMethod([
                'json_data'     => Mage::helper('core')->jsonEncode($this->_serviceParams),
                'method_name'   => $this->_serviceMethod
            ]);
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::log($e->getMessage());
        }

        return $dataArray;
    }

    /**
     * @param array $itemData
     *
     * @return array
     */
    protected function _removeNotUsedDataFields($itemData)
    {
        foreach ($this->_unusedDataFields as $toRemove) {
            unset($itemData[$toRemove]);
        }
        return $itemData;
    }

    /**
     * @param Mage_Catalog_Model_Category $parentCatalogCategoryModel
     * @param int $rootCategoryId
     *
     * @return string
     */
    private function _setCatalogCategoryParentId(
        Mage_Catalog_Model_Category $parentCatalogCategoryModel, $rootCategoryId
    )
    {
        $parentId = $rootCategoryId;
        if ($parentCatalogCategoryModel && $parentCatalogCategoryModel->getId()) {
            $parentId = $parentCatalogCategoryModel->getId();
        }

        return $parentId;
    }

    /**
     * @param int|null $id
     * @param Mage_Catalog_Model_Category $parentCatalogCategoryModel
     * @param int $rootCategoryId
     * @param Mage_Catalog_Model_Category $catalogCategoryModel
     * @return string
     */
    private function _setCatalogCategoryPath(
        $id,
        Mage_Catalog_Model_Category $parentCatalogCategoryModel,
        $rootCategoryId,
        Mage_Catalog_Model_Category $catalogCategoryModel
    ) {
        if (!$id) {
            $categoryPath = $rootCategoryId;
            if ($parentCatalogCategoryModel && $parentCatalogCategoryModel->getId()) {
                $categoryPath = rtrim($parentCatalogCategoryModel->getData('path'), '/');
            }
        } else {
            $basePath = $rootCategoryId;
            if ($parentCatalogCategoryModel && $parentCatalogCategoryModel->getId()) {
                $basePath = rtrim($parentCatalogCategoryModel->getData('path'), '/');
            }
            $basePath =  rtrim($basePath, '/');
            $path = $basePath . '/' . $catalogCategoryModel->getId();
            $path = rtrim($path, '/');
            $categoryPath = $path;
        }

        return $categoryPath;
    }

    /**
     * @param $catalogCategoryId
     */
    private function _assignAsDefaultCategory($catalogCategoryId)
    {
        /* @var $websiteModel Mage_Core_Model_Website */
        $websiteModel = Mage::getModel('core/website')->load(self::DEFAULT_WEBSITE_ID);

        /* @var $storeGroupCollection Mage_Core_Model_Mysql4_Store_Group_Collection */
        $storeGroupCollection = $websiteModel->getGroupCollection();

        /* @var $storeGroupModel Mage_Core_Model_Store_Group */
        foreach ($storeGroupCollection as $storeGroupModel) {
            $storeGroupModel->setRootCategoryId($catalogCategoryId);
            $storeGroupModel->save();
        }
    }
}
