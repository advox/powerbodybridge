<?php

/**
 * Class Powerbody_Manufacturer_Block_Product_List
 */
class Powerbody_Manufacturer_Block_Product_List extends Mage_Catalog_Block_Product_List
{

    /**
     * @return Powerbody_Manufacturer_Model_Layer
     */
    public function getLayer()
    {
        $currentLayer = Mage::registry('current_layer');
        if (true === ($currentLayer instanceof Powerbody_Manufacturer_Model_Layer)) {
            return $currentLayer;
        }
        return Mage::getSingleton('manufacturer/layer');
    }

    /**
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _getProductCollection()
    {
        if (null === $this->_productCollection) {
            $layer = $this->getLayer();
            $this->_productCollection = $layer->getProductCollection();
        }
        return $this->_productCollection;
    }

}
