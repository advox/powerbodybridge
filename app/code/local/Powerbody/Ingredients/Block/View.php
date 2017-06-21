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
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getSimpleProductCollectionForConfigurable()
    {
        /* @var $ingredientsProductLabelProvider Powerbody_Ingredients_Model_Provider_Product_Label */
        $ingredientsProductLabelProvider = Mage::getModel('ingredients/provider_product_label');
        /* @var $configurableProductModel Mage_Catalog_Model_Product */
        $configurableProductModel = $this->getCurrentProduct();

        return $ingredientsProductLabelProvider
            ->getSimpleProductCollectionForConfigurable($configurableProductModel);
    }

    /**
     * @return string
     */
    public function getIngredientUrl()
    {
        return Mage::getUrl('ingredients/label');
    }

    /**
     * @param Mage_Catalog_Model_Product $configurableProductModel
     *
     * @return bool
     */
    public function checkIngredientsLabelsForConfigurableExists(
        Mage_Catalog_Model_Product $configurableProductModel
    ) {
        if (null !== $configurableProductModel->getId()) {

            /* @var $ingredientsProductLabelProvider Powerbody_Ingredients_Model_Provider_Product_Label */
            $ingredientsProductLabelProvider = Mage::getModel('ingredients/provider_product_label');

            return $ingredientsProductLabelProvider
                ->checkIngredientsLabelsForConfigurableExists($configurableProductModel);
        }

        return false;
    }
}
