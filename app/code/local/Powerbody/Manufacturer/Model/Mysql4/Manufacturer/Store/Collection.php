<?php

/**
 * Class Powerbody_Manufacturer_Model_Mysql4_Manufacturer_Store_Collection
 */
class Powerbody_Manufacturer_Model_Mysql4_Manufacturer_Store_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    /**
     * construct
     */
    public function _construct()
    {
        $this->_init('manufacturer/manufacturer_store');
    }

}
