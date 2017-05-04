<?php

/**
 * Class Powerbody_Manufacturer_Model_Layer
 */
class Powerbody_Manufacturer_Model_Layer extends Varien_Object 
{

    /**
     * @var Mage_Catalog_Model_Resource_Product_Collection 
     */
    protected $_collection;
    
    /**
     * @var Powerbody_Manufacturer_Model_Manufacturer 
     */
    protected $_manufacturer;

    /**
     * @return int
     */
    public function getCacheKey() 
    {
        return $this->_getCurrentManufacturer()->getId();
    }
    
    /**
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getProductCollection() 
    {
        if (true !== ($this->_collection instanceof Mage_Catalog_Model_Resource_Product_Collection)) {
            $this->_collection = $this->_getProductCollection();
        }
        return $this->_collection;
    }

    /**
     * @return Powerbody_Manufacturer_Model_Manufacturer
     */
    protected function _getCurrentManufacturer() 
    {
        if (true !== ($this->_manufacturer instanceof Powerbody_Manufacturer_Model_Manufacturer)) {
            $this->_initializeCurrentManufacturer();
        }
        return $this->_manufacturer;
    }

    /**
     * Init manufacturer
     */
    protected function _initializeCurrentManufacturer() 
    {
        $this->_manufacturer = Mage::registry('current_manufacturer');
    }
    
    /**
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _getProductCollection() 
    {
        $productsCollection = $this->_getCurrentManufacturer()->getProductCollection();
        /* @var $productsCollection Mage_Catalog_Model_Resource_Product_Collection */
        $productsCollection
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents();
        
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($productsCollection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($productsCollection);
            
        return $productsCollection;
    }

    
}
