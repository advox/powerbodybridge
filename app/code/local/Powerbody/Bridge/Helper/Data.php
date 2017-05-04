<?php

/**
 * Class Powerbody_Bridge_Helper_Data
 */
class Powerbody_Bridge_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_CONFIG_ENABLE_MANUFACTURER_NOT_UPDATE_PRICE = 'bridge_settings/price_settings/enable_manufacturer_not_update_price';
    const XML_CONFIG_MANUFACTURER_NOT_UPDATE_PRICE = 'bridge_settings/price_settings/manufacturer_not_update_price';
    const XML_CONFIG_PRICE_CURRENCY_SETTINGS = 'bridge_settings/price_settings/price_currency_rate';

    /**
     * @return string
     */
    public function getBridgeIsEnabled()
    {
        return Mage::getStoreConfig('bridge_settings/basic_bridge_settings/enable');
    }

    /**
     * @return string
     */
    public function getDefaultCategoryName()
    {
        return Mage::getStoreConfig('bridge_settings/provider_settings/default_category_name');
    }

    /**
     * @return string
     */
    public function getDefaultStoreIdConfig()
    {
        return Mage::getStoreConfig('bridge_settings/provider_settings/default_store_id');
    }

    /**
     * @return string
     */
    public function getDefaultDestinationStoreIdConfig()
    {
        return Mage::getStoreConfig('bridge_settings/basic_bridge_settings/default_destination_store_id');
    }

    /**
     * @return string
     */
    public function getIsEnabledAsProvider()
    {
        return Mage::getStoreConfigFlag('bridge_settings/provider_settings/enable');
    }

    /**
     * @return string
     */
    public function getNumberOfHoursSinceLastChangeProducts()
    {
        return Mage::getStoreConfig('bridge_settings/basic_bridge_settings/number_of_hours_since_last_change_products');
    }

    /**
     * @return float
     */
    public function getPriceRate()
    {
        return (float) Mage::getStoreConfig(self::XML_CONFIG_PRICE_CURRENCY_SETTINGS);
    }

    /**
     * @param int|string $manufacturerId
     *
     * @return bool
     */
    public function isManufacturerShouldUpdatePrice($manufacturerId)
    {

        return !(Mage::getStoreConfig(self::XML_CONFIG_ENABLE_MANUFACTURER_NOT_UPDATE_PRICE)
            && in_array($manufacturerId, explode(',', Mage::getStoreConfig(self::XML_CONFIG_MANUFACTURER_NOT_UPDATE_PRICE))));
    }
}
