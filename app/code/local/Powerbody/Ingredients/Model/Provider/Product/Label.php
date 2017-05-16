<?php

/**
 * Class Powerbody_Ingredients_Model_Provider_Product_Label
 */
class Powerbody_Ingredients_Model_Provider_Product_Label
{
    /**
     * @param string $localeCode
     * @param array $labelData
     *
     * @return Powerbody_Ingredients_Model_Product_Label
     */
    public function getIngredientsProductLabelModelByLocaleAndSku($localeCode, array $labelData)
    {
        /* @var $catalogProductModel Mage_Catalog_Model_Product */
        $catalogProductModel = Mage::getModel("catalog/product");
        $productId = $catalogProductModel->getIdBySku($labelData['sku']);

        return Mage::getModel('ingredients/product_label')
            ->getCollection()
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('locale', $localeCode)
            ->getFirstItem();
    }

    /**
     * @param int $productId
     * @param string $localeCode
     * @return Powerbody_Ingredients_Model_Product_Label
     */
    public function getIngredientProductLabelModelForProductAndLocaleCode($productId, $localeCode)
    {
        $ingredientsProductLabelModel = Mage::getModel('ingredients/product_label')
            ->getCollection()
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('locale', $localeCode)
            ->getFirstItem();

        return $ingredientsProductLabelModel;
    }
}
