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

    /**
     * @param Mage_Catalog_Model_Product $configurableProductModel
     *
     * @return bool
     */
    public function checkIngredientsLabelsForConfigurableExists(Mage_Catalog_Model_Product $configurableProductModel)
    {
        /* @var $simpleProductCollection Mage_Catalog_Model_Resource_Product_Collection */
        $simpleProductCollection = $this->getSimpleProductCollectionForConfigurable($configurableProductModel);

        if ($simpleProductCollection->getSize() > 0) {
            $simpleProductArray = $simpleProductCollection->getColumnValues('entity_id');
            /* @var $ingredientsLabelCollection Powerbody_Ingredients_Model_Mysql4_Product_Label_Collection */
            $ingredientsLabelCollection = Mage::getModel('ingredients/product_label')->getCollection();
            $ingredientsLabelCollection->addFieldToFilter('product_id', ['in' => $simpleProductArray]);
            $ingredientsLabelCollection->addFieldToFilter(
                'status', Powerbody_Ingredients_Model_Product_Label::STATUS_UP_TO_DATE
            );
            $ingredientsLabelCollection->addFieldToFilter(
                'locale', Mage::app()->getLocale()->getLocaleCode()
            );

            return boolval($ingredientsLabelCollection->getSize());
        }

        return false;
    }

    /**
     * @param Mage_Catalog_Model_Product $configurableProductModel
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getSimpleProductCollectionForConfigurable(Mage_Catalog_Model_Product $configurableProductModel)
    {
        /* @var $resourceModel Mage_Core_Model_Resource */
        $resourceModel = Mage::getSingleton('core/resource');
        /* @var $catalogProductTypeConfigurableModel Mage_Catalog_Model_Product_Type_Configurable */
        $catalogProductTypeConfigurableModel = Mage::getModel('catalog/product_type_configurable');
        /* @var $configurableTypeModel Mage_Catalog_Model_Product_Type_Configurable */
        $configurableTypeModel = $catalogProductTypeConfigurableModel
            ->setProduct($configurableProductModel);
        /* @var $simpleProductCollection Mage_Catalog_Model_Resource_Product_Collection */
        $simpleProductCollection = $configurableTypeModel->getUsedProductCollection()
            ->addAttributeToSelect('status')
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
            ->joinField('qty',
                $resourceModel->getTableName('cataloginventory/stock_item'),
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left')
            ->addAttributeToFilter('qty', ["gt" => 0]);
        ;

        return $simpleProductCollection;
    }
}
