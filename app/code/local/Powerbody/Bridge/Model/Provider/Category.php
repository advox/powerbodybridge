<?php

/**
 * Class Powerbody_Bridge_Model_Provider_Category
 */
class Powerbody_Bridge_Model_Provider_Category
{
    const ROOT_PARENT_CATEGORY_ID = 1;

    /**
     * @var array $categoriesIds
     *
     * @return Powerbody_Bridge_Model_Mysql4_Imported_Category_Collection
     */
    public function getImportedCategoriesToSave($categoriesIds)
    {
        /* @var $bridgeImportedCategoryCollection Powerbody_Bridge_Model_Mysql4_Imported_Category_Collection */
        $bridgeImportedCategoryCollection = Mage::getModel('bridge/imported_category')->getCollection();
        return $bridgeImportedCategoryCollection->addFieldToFilter('id', ['in' => $categoriesIds]);
    }

    /**
     * @return array
     */
    public function getBeforeCleanImportedCategoryClientIds()
    {
        /* @var $importedCategoryCollection Powerbody_Bridge_Model_Mysql4_Imported_Category_Collection */
        $importedCategoryCollection = Mage::getModel('bridge/imported_category')->getCollection();
        $importedCategoryCollection->addFieldToFilter('is_selected', 1);

        return $importedCategoryCollection->getData();
    }

    /**
     * @param array $categoriesArray
     * @return array
     */
    public function prepareImportedCategoriesForWebService($categoriesArray)
    {
        /* @var $importedCategoryCollection Powerbody_Bridge_Model_Mysql4_Imported_Category_Collection */
        $importedCategoryCollection = Mage::getModel('bridge/imported_category')->getCollection()
            ->addFieldToFilter('id', array('in' => $categoriesArray));
        $importedCategoryPaths = $importedCategoryCollection->getColumnValues('path');
        $importedCategoryIds = [];
        foreach ($importedCategoryPaths as $importedCategoryPath) {
            $importedCategoryArray = explode('/', $importedCategoryPath);
            foreach ($importedCategoryArray as $importedCategoryId) {
                $importedCategoryIds[$importedCategoryId]['id'] = $importedCategoryId;
            }
        }
        unset($importedCategoryIds[self::ROOT_PARENT_CATEGORY_ID]);

        return $importedCategoryIds;
    }

    /**
     * @param array $originalData
     * @param array $postData
     *
     * @return array
     */
    public function getCatalogCategoriesToDelete($originalData, $postData)
    {
        $tmpOriginalData = [];
        foreach ($originalData as $original) {
            $tmpOriginalData[$original['id']] = $original['id'];
        }
        if (empty($postData) === true) {
            $originalData = $this->getImportedCategoryData();
            $categoriesToDelete = array_column($originalData, 'id');
        } else {
            $categoriesToDelete = array_diff($tmpOriginalData, $postData);
        }
        $catalogCategoriesBaseIds = [];
        foreach ($originalData as $original) {
            if (true === in_array($original['id'], $categoriesToDelete)) {
                $catalogCategoriesBaseIds[$original['id']]['id'] = $original['id'];
                $catalogCategoriesBaseIds[$original['id']]['base'] = $original['base_category_id'];
                $catalogCategoriesBaseIds[$original['id']]['client'] = $original['client_category_id'];
            }
        }

        return $catalogCategoriesBaseIds;
    }

    /**
     * @return array
     */
    public function getImportedCategoryData()
    {
        /* @var $importedCategoryCollection Powerbody_Bridge_Model_Mysql4_Imported_Category_Collection */
        $importedCategoryCollection = Mage::getModel('bridge/imported_category')->getCollection();
        $importedCategoryCollection->addFieldToFilter('client_category_id', ['notnull' => true]);

        return $importedCategoryCollection->getData();
    }
}
