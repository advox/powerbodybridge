<?php

/**
 * Class Powerbody_Bridge_Model_Provider_Imported_Categories
 */
class Powerbody_Bridge_Model_Provider_Imported_Categories
{
    /* @var null|array */
    private $_importedCategories = null;

    /**
     * @return Powerbody_Bridge_Model_Mysql4_Imported_Category_Collection
     */
    public function getImportedCategories()
    {
        if (false === is_array($this->_importedCategories)) {
            $importedCategoriesCollection = Mage::getModel('bridge/imported_category')->getCollection();
            $importedCategoriesCollection->removeAllFieldsFromSelect();
            $importedCategoriesCollection->addFieldToSelect(['is_selected', 'base_category_id', 'client_category_id']);
            $this->_importedCategories = $importedCategoriesCollection->getData();
        }

        return $this->_importedCategories;
    }

    /**
     * @return array
     */
    public function getSelectedImportedCategories()
    {
        $selectedImportedCategories = [];
        $importedCategoriesArray = $this->getImportedCategories();
        foreach ($importedCategoriesArray as $importedCategory) {
            if ($importedCategory['is_selected'] === '1') {
                $selectedImportedCategories[] = $importedCategory;
            }
        }

        return $selectedImportedCategories;
    }

    /**
     * @return array
     */
    public function getSelectedImportedCategoriesBaseIds()
    {
        $importedCategoriesArray = $this->getSelectedImportedCategories();

        return array_column($importedCategoriesArray, 'base_category_id');
    }

    /**
     * @return array
     */
    public function getSelectedImportedCategoriesInternalIds()
    {
        $importedCategoriesArray = $this->getSelectedImportedCategories();

        return array_column($importedCategoriesArray, 'client_category_id');

    }

    /**
     * @return array
     */
    public function getNotSelectedImportedCategories()
    {
        $notSelectedImportedCategories = [];
        $importedCategoriesArray = $this->getImportedCategories();
        foreach ($importedCategoriesArray as $importedCategory) {
            if ($importedCategory['is_selected'] === '0') {
                $notSelectedImportedCategories[] = $importedCategory;
            }
        }

        return $notSelectedImportedCategories;
    }

    /**
     * @return array
     */
    public function getNotSelectedImportedCategoriesInternalIds()
    {
        $importedCategoriesArray = $this->getNotSelectedImportedCategories();

        return array_column($importedCategoriesArray, 'client_category_id');
    }
}
