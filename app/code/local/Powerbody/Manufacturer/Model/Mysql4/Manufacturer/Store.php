<?php

/**
 * Class Powerbody_Manufacturer_Model_Mysql4_Manufacturer_Store
 */
class Powerbody_Manufacturer_Model_Mysql4_Manufacturer_Store extends Mage_Core_Model_Mysql4_Abstract
{

    /**
     * construct
     */
    protected function _construct()
    {
        $this->_init('manufacturer/manufacturer_store', 'id');
    }

}
