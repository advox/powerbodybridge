<?php

/**
 * Class Powerbody_Manufacturer_Model_Event_Manufacturer_Observer
 */
class Powerbody_Manufacturer_Model_Event_Manufacturer_Observer
{
    
    /**
     * Event observer <controller_front_init_routers>
     * @param Varien_Event_Observer $observer
     * @return \Powerbody_Manufacturer_Model_Event_Manufacturer_Observer
     */
    public function changeManufacturerRoute(Varien_Event_Observer $observer)
    {
        $observer->getEvent()->getFront()
            ->addRouter('manufacturer', new Powerbody_Manufacturer_Controller_Router());
        return $this;
    }
    
    /**
     * Event observer <controller_action_predispatch>
     * @param Varien_Event_Observer $observer
     * @return \Powerbody_Manufacturer_Model_Event_Manufacturer_Observer
     */
    public function checkSession(Varien_Event_Observer $observer) 
    {
        $moduleName = $observer->getEvent()->getData('controller_action')
            ->getRequest()->getModuleName();
        if ('manufacturer' !== $moduleName) {
            Mage::getSingleton('core/session')->setData('manufacturer', null);
        }
        return $this;
    }
    
    /**
     * Event observer <clean_catalog_images_cache_after>
     * @param Varien_Event_Observer $observer
     * @return \Powerbody_Manufacturer_Model_Event_Manufacturer_Observer
     */
    public function clearManufacturerImageCache(Varien_Event_Observer $observer)
    {
        $directory = Mage::getBaseDir('media') . DS . 'manufacturer' . DS . 'cache' . DS;
        $io = new Varien_Io_File();
        $io->rmdir($directory, true);
        Mage::app()->cleanCache();
        return $this;
    }
    
}
