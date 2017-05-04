<?php

/**
 * Class Powerbody_Manufacturer_Controller_Router
 */
class Powerbody_Manufacturer_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract 
{

    /**
     * Match route for manufacturers.
     * @param \Zend_Controller_Request_Http $request
     * @return bool
     */
    public function match(\Zend_Controller_Request_Http $request) 
    {
        $requestUri = $request->getRequestUri();
        $urlSuffix = Mage::getStoreConfig('catalog/manufacturer/url_suffix');
        $urlKey = $requestUri;
        
        if (true !== empty($urlSuffix)) {
            $urlKey = str_replace($urlSuffix, '', $requestUri);
        }
        
        $urlKey = substr($urlKey, 1);
        if (true === isset($urlKey)) {
            $urlKey = explode('?', $urlKey);
            if (true === isset($urlKey[0])) {
                $manufacturerId = Mage::getSingleton('manufacturer/provider_manufacturer')
                    ->getManufacturerIdByUrlKey($urlKey[0]);
                if (null !== $manufacturerId) {
                    $request->setModuleName('manufacturer')
                        ->setControllerName('manufacturer')
                        ->setActionName('view')
                        ->setParam('key', $manufacturerId);
                    return true;
                }
            }
        }
        
        return false;
    }

}
