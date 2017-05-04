<?php

/**
 * Class Powerbody_Manufacturer_Model_Mysql4_Product
 */
class Powerbody_Manufacturer_Model_Mysql4_Product extends Mage_Core_Model_Mysql4_Abstract
{

    /**
     * construct
     */
    protected function _construct()
    {
        $this->_init('manufacturer/product', 'id');
    }

}
