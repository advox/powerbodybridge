<?php

/**
 * Class Powerbody_Manufacturer_Model_Mysql4_Product_Collection
 */
class Powerbody_Manufacturer_Model_Mysql4_Product_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    /**
     * construct
     */
    protected function _construct()
    {
        $this->_init('manufacturer/product');
    }

}
