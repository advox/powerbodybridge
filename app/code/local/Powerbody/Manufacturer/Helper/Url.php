<?php

/**
 * Class Powerbody_Manufacturer_Helper_Url
 */
class Powerbody_Manufacturer_Helper_Url extends Mage_Core_Helper_Abstract
{
    
    const MANUFACTURER_ID = '[manufacturer_id]';
    const MANUFACTURER_NAME = '[manufacturer_name]';
    const URL_PREFIX = '[url_prefix]';
    
    /**
     * @param Powerbody_Manufacturer_Model_Manufacturer $manufacturer
     * @param int $storeId
     * @return string
     */
    public function generateUrl(Powerbody_Manufacturer_Model_Manufacturer $manufacturer, $storeId)
    {
        $urlFormat = Mage::getStoreConfig('catalog/manufacturer/url_format', $storeId);
        $urlPrefix = Mage::getStoreConfig('catalog/manufacturer/url_prefix', $storeId);
        
        $url = str_replace(self::MANUFACTURER_ID, $manufacturer->getId(), $urlFormat);
        $url = str_replace(self::MANUFACTURER_NAME, $this->_formatString($manufacturer->getData('name')), $url);
        $url = str_replace(self::URL_PREFIX, $this->_formatString($urlPrefix), $url);
        
        return $url;
    }
    
    /**
     * @param string $string
     * @return string
     */
    protected function _formatString($string) 
    {
        $formatted = $string;
        $formatted = preg_replace('/[^a-z0-9_]/i', '-', $formatted);
        $formatted = preg_replace('/-[-]*/i', '-', $formatted);
        $formatted = rtrim($formatted, '-');
        $formatted = trim($formatted);
        return strtolower($formatted);
    }
    
}
