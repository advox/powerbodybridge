<?php

/**
 * Class Powerbody_Bridge_Helper_Manufacturer
 */
class Powerbody_Bridge_Helper_Manufacturer extends Mage_Core_Helper_Abstract
{
    private $manufacturers = [];
    
    public function __construct()
    {
        /** @var $manufacturersCollection Powerbody_Bridge_Model_Mysql4_Imported_Manufacturer_Collection */
        $manufacturersCollection = Mage::getModel('bridge/imported_manufacturer')->getCollection();

        foreach ($manufacturersCollection as $manufacturerModel) { /* @var $manufacturerModel Powerbody_Bridge_Model_Imported_Manufacturer */
            $this->manufacturers[$manufacturerModel->getId()] = $manufacturerModel->getName();
        }
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
