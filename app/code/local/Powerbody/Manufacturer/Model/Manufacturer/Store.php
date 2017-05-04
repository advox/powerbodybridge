<?php

/**
 * Class Powerbody_Manufacturer_Model_Manufacturer_Store
 */
class Powerbody_Manufacturer_Model_Manufacturer_Store extends Mage_Core_Model_Abstract
{
    const DEFAULT_MANUFACTURER_STORE_ID = 1;

    /**
     * construct
     */
    protected function _construct()
    {
        $this->_init('manufacturer/manufacturer_store');
    }
}
