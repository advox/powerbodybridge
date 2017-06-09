<?php

/**
 * Class Powerbody_Bridge_Helper_Manufacturer
 */
class Powerbody_Bridge_Helper_Manufacturer extends Mage_Core_Helper_Abstract
{
    private $manufacturers = [];
    
    public function __construct()
    {
        $this->manufacturers = Mage::getSingleton('bridge/provider_manufacturer')->getManufacturersArray();
    }
    
    /**
     * @param  string $manufacturerId
     * @return string
     */
    public function getManufacturerNameById($manufacturerId)
    {
        if (true === key_exists($manufacturerId, $this->manufacturers)) {
            return $this->manufacturers[$manufacturerId];
        }
        
        return '';
    }
}
