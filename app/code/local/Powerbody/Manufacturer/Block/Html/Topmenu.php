<?php

/**
 * Class Powerbody_Manufacturer_Block_Html_Topmenu
 */
class Powerbody_Manufacturer_Block_Html_Topmenu 
    extends Mage_Core_Block_Template
{

    /**
     * @return Powerbody_Manufacturer_Model_Mysql4_Manufacturer_Collection
     */
    protected function getManufacturerCollection()
    {
        return Mage::getSingleton('manufacturer/provider_manufacturer')
            ->getCollectionSortByName();
    }

}
