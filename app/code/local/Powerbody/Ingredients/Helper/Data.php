<?php

/**
 * Class Powerbody_Ingredients_Helper_Data
 */
class Powerbody_Ingredients_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * get bridge is enabled
     * @return bollean
     */
    public function getBridgeIsEnabled()
    {
        return Mage::getStoreConfig('bridge_settings/basic_bridge_settings/enable');
    }
}
