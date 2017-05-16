<?php

/**
 * Class Powerbody_Ingredients_Block_View
 */
class Powerbody_Ingredients_Block_View extends Mage_Core_Block_Template
{
    /**
     * Powerbody_Ingredients_Block_View constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/label/image.phtml');
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getCurrentProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * @return array
     */
    public function getSimpleProductCollectionForConfigurable()
    {
        /* @var $configurableProductModel Mage_Catalog_Model_Product */
        $configurableProductModel = $this->getCurrentProduct();
        /* @var $configurableTypeModel Mage_Catalog_Model_Product_Type_Configurable */
        $configurableTypeModel = Mage::getModel('catalog/product_type_configurable');

        return $configurableTypeModel->getUsedProducts(null,$configurableProductModel);
    }

    /**
     * @return string
     */
    public function getIngredientUrl()
    {
        return Mage::getUrl('ingredients/label');
    }
}
