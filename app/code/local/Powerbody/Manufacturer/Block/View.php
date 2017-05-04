<?php

/**
 * Class Powerbody_Manufacturer_Block_View
 */
class Powerbody_Manufacturer_Block_View extends Mage_Core_Block_Template
{

    /**
     * @var Powerbody_Manufacturer_Model_Manufacturer
     */
    protected $_manufacturer;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->_manufacturer = Mage::registry('current_manufacturer');
        $this->_addManufacturerToSession();
    }

    /**
     * @return Powerbody_Manufacturer_Model_Manufacturer
     */
    public function getManufacturer()
    {
        return $this->_manufacturer;
    }
    
    /**
     * Add manufacturer id to session.
     */
    protected function _addManufacturerToSession()
    {
        Mage::getSingleton('core/session')
            ->setData('manufacturer', $this->_manufacturer->getId());
    }

    /**
     * Prepare layout. Add breadcrumbs to page.
     */
    protected function _prepareLayout()
    {
        if (true == ($this->_manufacturer instanceof Powerbody_Manufacturer_Model_Manufacturer)) {
            if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbs->addCrumb('home', array(
                    'label' => $this->__('Home'),
                    'link'  => Mage::getBaseUrl(),
                    'title' => $this->__('Go to Home Page'),
                ));
                $breadcrumbs->addCrumb('manufacturer_name', array(
                    'label' => $this->_manufacturer->getName(), 
                    'title' => $this->_manufacturer->getName()
                ));
            }
        }
        parent::_prepareLayout();
    }

}
