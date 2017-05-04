<?php

/**
 * Class Powerbody_Manufacturer_Block_Product_List_Toolbar
 */
class Powerbody_Manufacturer_Block_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar 
{

    /**
     * Return current URL with rewrites and additional parameters
     *
     * @param array $params Query parameters
     * @return string
     */
    public function getPagerUrl($params = array()) 
    {
        $currentManufacturerUrl = Mage::registry('current_manufacturer')->getUrlKey();
        $url = '';
        $url = Mage::helper('core/url')->addRequestParam($currentManufacturerUrl, $params);
        return $url;
    }

}
