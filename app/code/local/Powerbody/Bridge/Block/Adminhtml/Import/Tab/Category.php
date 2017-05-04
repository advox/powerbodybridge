<?php

/**
 * Class Powerbody_Bridge_Block_Adminhtml_Import_Tab_Category
 */
class Powerbody_Bridge_Block_Adminhtml_Import_Tab_Category extends Mage_Adminhtml_Block_Template
{
    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('bridge/import/category.phtml');
    }

    /**
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('*/*/categorySave');
    }

    /**
     * @return array
     */
    public function getImportedCategories()
    {
        /* @var $importedCategoriesCollection Powerbody_Bridge_Model_Mysql4_Imported_Category_Collection */
        $importedCategoriesCollection = Mage::getModel('bridge/imported_category')->getCollection();

        return $this->_buildImportedCategoryTree($importedCategoriesCollection->getData());
    }

    /**
     * @param array $importedCategory
     *
     * @return mixed
     */
    public function renderImportedCategoryRow($importedCategory)
    {
        return Mage::helper('bridge/import')->renderImportedCategoryRow($importedCategory);
    }

    /**
     * @param array $importedCategories
     *
     * @return array
     */
    protected function _buildImportedCategoryTree($importedCategories)
    {
        $categoryChildren = array();
        foreach($importedCategories as &$importedCategory) {
            $categoryChildren[$importedCategory['parent_id']][] = &$importedCategory;
        }
        unset($importedCategory);
        foreach($importedCategories as &$importedCategory)
        {
            if (isset($categoryChildren[$importedCategory['base_category_id']])){
                $importedCategory['children'] = $categoryChildren[$importedCategory['base_category_id']];
            }
        }

        return $categoryChildren[Powerbody_Bridge_Model_Provider_Category::ROOT_PARENT_CATEGORY_ID];
    }
}
