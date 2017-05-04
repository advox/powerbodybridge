<?php

/**
 * Class Powerbody_Manufacturer_Block_Html_Pager
 */
class Powerbody_Manufacturer_Block_Html_Pager extends Mage_Page_Block_Html_Pager
{

    /**
     * @param array $params
     * @return string
     */
    public function getPagerUrl($params = array())
    {
        $urlParams = array();
        $urlParams['_current'] = true;
        $urlParams['_escape'] = true;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_query'] = $params;
        return $this->_getManufacturerUrl($params);
    }
    
    /**
     * @param array $params
     * @return string
     */
    protected function _getManufacturerUrl($params = array())
    {
        $path = substr($this->getRequest()->getPathInfo(), 1);
        return Mage::getBaseUrl() . $path . '?p=' . $params['p'];
    }

}
