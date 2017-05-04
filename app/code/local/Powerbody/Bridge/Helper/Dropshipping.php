<?php

/**
 * Class Powerbody_Bridge_Helper_Dropshipping
 */
class Powerbody_Bridge_Helper_Dropshipping extends Mage_Core_Helper_Abstract
{
    const CONFIG_PREFIX = 'bridge_settings/dropshipping/';
    
    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->_getConfig('api_key');
    }
    
    /**
     * @return string
     */
    public function getApiUserName()
    {
        return $this->_getConfig('api_user_name');
    }
    
    /**
     * @return string
     */
    public function getApiWsdlFile()
    {
        return $this->_getConfig('wsdl_url');
    }
    
    /**
     * @return bool
     */
    public function getIsEnabled()
    {
        return Mage::getStoreConfigFlag(self::CONFIG_PREFIX . 'enable');
    }
    
    /**
     * @return string
     */
    public function getShippingMethodId()
    {
        return $this->_getConfig('shipping_method_id');
    }

    /**
     * @return array
     */
    public function getApiConfigData()
    {
        return [
            'api_user_name' => $this->getApiUserName(),
            'api_key'       => $this->getApiKey(),
            'api_wsdl'      => $this->getApiWsdlFile(),
        ];
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function _getConfig($path)
    {
        return Mage::getStoreConfig(self::CONFIG_PREFIX . $path);
    }
}
