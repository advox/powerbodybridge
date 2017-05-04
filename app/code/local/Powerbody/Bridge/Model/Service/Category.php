<?php

/**
 * Class Powerbody_Bridge_Model_Service_Category
 */
class Powerbody_Bridge_Model_Service_Category extends Mage_Core_Model_Abstract
{
    /**
     * @param array $postCategoriesArray
     *
     * @throws Exception
     */
    public function processImportedCategoriesSave(array $postCategoriesArray)
    {
        try {
            $this->_forceProductsUpdate();
            /* @var $providerCategory Powerbody_Bridge_Model_Provider_Category */
            $providerCategory = Mage::getSingleton('bridge/provider_category');
            /* @var $categorySyncModel Powerbody_Bridge_Model_Sync_Category */
            $categorySyncModel = Mage::getModel('bridge/sync_category');
            $beforeCleanClientCategoryIds = $providerCategory->getBeforeCleanImportedCategoryClientIds();
            $catalogCategoriesToDelete = $providerCategory
                ->getCatalogCategoriesToDelete($beforeCleanClientCategoryIds, $postCategoriesArray);
            if (false === empty($catalogCategoriesToDelete)) {
                $this->_deleteCatalogCategories($catalogCategoriesToDelete);
            }
            $this->_saveImportedCategories($postCategoriesArray);
            if (false === empty($postCategoriesArray)) {
                $baseCategoryIds = $providerCategory->prepareImportedCategoriesForWebService($postCategoriesArray);
                $categorySyncModel->processCatalogCategoriesByIds($baseCategoryIds);
            }
        } catch (Exception $e) {
            Mage::logException($e);
            throw (new Exception($e->getMessage()));
        }
    }

    /**
     * @return \Powerbody_Bridge_Model_Service_Category
     */
    public function sortAlphabetically()
    {
        $categoryCollection = Mage::getResourceModel('catalog/category_collection')
        /* @var $categoryCollection Mage_Catalog_Model_Resource_Category_Collection */
            ->addAttributeToSelect('name')
            ->setOrder('name');
        $writeAdapter = $this->_getAdapter();
        /* @var $writeAdapter Magento_Db_Adapter_Pdo_Mysql */
        $tableName = Mage::getSingleton('core/resource')->getTableName('catalog_category_entity');
        $writeAdapter->beginTransaction();
        try {
            $position = 1;
            foreach ($categoryCollection as $categoryModel) {
                /* @var $categoryModel Mage_Catalog_Model_Category */
                $writeAdapter->update($tableName, ['position' => $position++], 'entity_id = ' . $categoryModel->getId());
            }
            $writeAdapter->commit();
        } catch (Exception $e) {
            $writeAdapter->rollback();
            Mage::logException($e);
        }
        
        return $this;
    }

    /**
     * @param array $postCategoriesArray
     */
    protected function _saveImportedCategories(array $postCategoriesArray)
    {
        $this->_clearImportedCategoriesSelected();
        /* @var categoryProvider Powerbody_Bridge_Model_Provider_Category */
        $categoryProvider = Mage::getModel('bridge/provider_category');
        /* @var $importedCategories Powerbody_Bridge_Model_Mysql4_Imported_Category_Collection */
        $importedCategories = $categoryProvider->getImportedCategoriesToSave($postCategoriesArray);
        foreach ($importedCategories as $importedCategoryModel) {
            /* @var $importedCategoryModel Powerbody_Bridge_Model_Imported_Category */
            $importedCategoryModel->setData('is_selected', 1)
                ->save();
        }
    }

    /**
     * @param array $categories
     */
    protected function _deleteCatalogCategories($categories)
    {
        foreach ($categories as $category) {
            if ($category['client'] != Mage::app()->getStore("default")->getRootCategoryId()) {
                $this->_deleteBridgeImportedCategory($category['id']);
                $this->_deleteCatalogCategory($category);
            }
        }
    }

    /**
     * @param array $category
     */
    protected function _deleteCatalogCategory($category)
    {
        /* @var $catalogCategoryModel Mage_Catalog_Model_Category */
        $catalogCategoryModel = Mage::getModel('catalog/category')->load($category['client']);
        $catalogCategoryModel->delete();
    }

    /**
     * @param int $categoryId
     */
    protected function _deleteBridgeImportedCategory($categoryId)
    {
        /* @var $importedCategoryModel Powerbody_Bridge_Model_Imported_Category */
        $importedCategoryModel = Mage::getModel('bridge/imported_category')->load($categoryId);
        $importedCategoryModel
            ->setData('client_category_id', new Zend_Db_Expr('null'))
            ->setData('is_selected', 0)
            ->save();
    }

    /**
     * Clear imported categories selected
     */
    protected function _clearImportedCategoriesSelected()
    {
        /* @var $importedCategoryCollection Powerbody_Bridge_Model_Mysql4_Imported_Category_Collection */
        $importedCategoryCollection = Mage::getModel('bridge/imported_category')->getCollection();
        foreach ($importedCategoryCollection as $importedCategoryModel) {
            $importedCategoryModel
                ->setData('is_selected', 0)
                ->save();
        }
    }

    private function _forceProductsUpdate()
    {
        $this->_getAdapter()->update('catalog_product_entity', ['updated_at' => null]);
    }

    /**
     * @return Varien_Db_Adapter_Interface
     */
    private function _getAdapter()
    {
        return Mage::getSingleton('core/resource')
            ->getConnection('core_write');
    }
}
