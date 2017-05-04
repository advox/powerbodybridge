<?php

/**
 * Class Powerbody_Manufacturer_Model_Provider_Product
 */
class Powerbody_Manufacturer_Model_Provider_Product
{
    
    /**
     * @param int $manufacturerId
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getProductCollection($manufacturerId)
    {
        $productsCollection = Mage::getModel('catalog/product')
            ->getCollection();
        /* @var $productsCollection Mage_Catalog_Model_Resource_Product_Collection */
        $this->_addManufacturerFilterToProductsCollection($productsCollection, $manufacturerId);
        return $productsCollection;
    }
    
    /**
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @param int $manufacturerId
     */
    public function _addManufacturerFilterToProductsCollection(
        Mage_Catalog_Model_Resource_Product_Collection$collection, $manufacturerId)
    {
        $collection->getSelect()
            ->joinLeft(
                array(
                    'mp' => Mage::getSingleton('core/resource')->getTableName('manufacturer/product')
                ),
                'e.entity_id = mp.product_id',
                array(
                    'mp.manufacturer_id'
                )
            )
            ->where('mp.manufacturer_id = ?', $manufacturerId);
    }
    
}
