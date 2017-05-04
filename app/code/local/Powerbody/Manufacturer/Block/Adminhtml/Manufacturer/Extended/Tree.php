<?php

/**
 * Class Powerbody_Manufacturer_Block_Adminhtml_Manufacturer_Extended_Tree
 */
class Powerbody_Manufacturer_Block_Adminhtml_Manufacturer_Extended_Tree extends Mage_Core_Block_Template
{

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->setUseAjax(true);
    }

    /**
     * Get url on manufacturer click.
     * @param int $manufacturerId
     * @return string
     */
    public function getListUrl($manufacturerId)
    {
        return Mage::helper('adminhtml')->getUrl('*/*/*', array(
            'id' => $manufacturerId
        ));
    }

    /**
     * @return Powerbody_Manufacturer_Model_Mysql4_Manufacturer_Collection
     */
    public function getManufacturersCollection()
    {
        return Mage::getSingleton('manufacturer/provider_manufacturer')
            ->getCollectionSortByName();
    }
    
}
