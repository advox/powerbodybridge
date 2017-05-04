<?php

/**
 * Class Powerbody_Manufacturer_ManufacturerController
 */
class Powerbody_Manufacturer_ManufacturerController extends Mage_Core_Controller_Front_Action
{

    /**
     * Display manufacturer list action.
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    /**
     * Manufacturer products list action.
     */
    public function viewAction()
    {
        $this->_initManufacturer();
        
        if (null === Mage::registry('current_manufacturer')->getId()) {
            $this->_forward('noRoute');
        } else {
            $this->loadLayout();
            $this->_initLayoutMessages('catalog/session');
            $this->renderLayout();
        }
    }
    
    /**
     * Load manufacturer model and save it in registry.
     */
    protected function _initManufacturer()
    {
        $manufacturerModel = Mage::getSingleton('manufacturer/provider_manufacturer')
            ->getStoreManufacturerById(
                $this->_removeAdditionalParameters($this->getRequest()->getParam('key')),
                Mage::app()->getStore()->getId()
            );
        /* @var $manufacturerModel Powerbody_Manufacturer_Model_Manufacturer */
        Mage::register('current_manufacturer', $manufacturerModel);
    }

    /**
     * Remove filters params from url.
     * @param string $key
     * @return string
     */
    protected function _removeAdditionalParameters($key)
    {
        $exploded = explode('?', $key);
        if (true === isset($exploded[0])) {
            return $exploded[0];
        }
        return $key;
    }

}
