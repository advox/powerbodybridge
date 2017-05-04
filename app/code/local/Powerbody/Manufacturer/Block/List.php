<?php

/**
 * Class Powerbody_Manufacturer_Block_List
 */
class Powerbody_Manufacturer_Block_List extends Mage_Core_Block_Template
{

    /**
     * Prepare layout. Add page breadcrumbs.
     */
    protected function _prepareLayout()
    {
        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('home', array(
                'label' => $this->__('Home'), 
                'title' => $this->__('Go to Home Page'), 
                'link' => Mage::getBaseUrl())
            );
            $breadcrumbs->addCrumb('manufacturer_name', array(
                'label' => 'Brands', 
                'title' => 'Brands')
            );
        }
        parent::_prepareLayout();
    }

    /**
     * @return Powerbody_Manufacturer_Model_Mysql4_Manufacturer_Collection
     */
    protected function getManufacturerCollection()
    {
        return Mage::getSingleton('manufacturer/provider_manufacturer')
            ->getStoreManufacturersCollection();
    }

}
