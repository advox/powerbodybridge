<?php

require_once Mage::getBaseDir() . DS . 'lib/Dropshipping/Api.php';

/**
 * Class Powerbody_Bridge_Model_Dropshipping_Abstract
 */
abstract class Powerbody_Bridge_Model_Dropshipping_Abstract 
{
    /**
     * @return array
     */
    protected function _getApiConfigData()
    {
        return $this->_getHelper()->getApiConfigData();
    }
    
    /**
     * @return Powerbody_Bridge_Helper_Dropshipping
     */
    protected function _getHelper()
    {
        return Mage::helper('bridge/dropshipping');
    }
}
