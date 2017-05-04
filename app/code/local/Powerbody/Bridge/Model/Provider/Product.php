<?php

/**
 * Class Powerbody_Bridge_Model_Provider_Product
 */
class Powerbody_Bridge_Model_Provider_Product
{
    /**
     * @param array $skuArray
     *
     * @return array
     */
    public function getProductsIdsWithoutMatchingSKU(array $skuArray)
    {
        $productIds = [];

        /* @var $productCollection Mage_Catalog_Model_Resource_Product_Collection */
        $productCollection = Mage::getModel('catalog/product')->getCollection();

        $productCollection->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(['entity_id', 'sku']);
        $productCollection
            ->addAttributeToSelect(['is_imported', 'type_id'])
            ->addAttributeToFilter('is_imported', 1)
            ->addAttributeToFilter('type_id', Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
        ;

        foreach ($productCollection as $productModel) {
            if (false === in_array($productModel->getData('sku'), $skuArray)) {
                $productIds[] = $productModel->getId();
            }
        }

        return $productIds;
    }

    /**
     * @param array $skuArray
     *
     * @return array
     */
    public function getProductsIdsFromSKUArray(array $skuArray)
    {
        /* @var $productCollection Mage_Catalog_Model_Resource_Product_Collection */
        $productCollection = Mage::getModel('catalog/product')->getCollection();

        $productCollection->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(['entity_id', 'sku']);

        $productCollection->addAttributeToFilter('sku', ['in' => $skuArray]);

        return $productCollection->getColumnValues('entity_id');
    }

    /**
     * @return array
     */
    public function getImportedProductsSKUArray()
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $productCollection */
        $productCollection = Mage::getModel('catalog/product')->getCollection();
        $productCollection->addAttributeToSelect(['is_imported', 'sku'])
            ->addAttributeToFilter('is_imported', 1)
        ;

        return $productCollection->getColumnValues('sku');
    }

    public function getImportedProductsToUpdateSkuArray()
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $productCollection */
        $productCollection = Mage::getModel('catalog/product')->getCollection();
        $productCollection->addAttributeToSelect(['is_imported', 'sku'])
            ->addAttributeToFilter('is_imported', 1)
            ->addAttributeToFilter('is_updated_while_import', 1)
        ;

        return $productCollection->getColumnValues('sku');
    }
}
