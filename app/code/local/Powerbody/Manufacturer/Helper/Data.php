<?php

/**
 * Class Powerbody_Manufacturer_Helper_Data
 */
class Powerbody_Manufacturer_Helper_Data extends Mage_Core_Helper_Abstract
{
    const DEFAULT_EXTERNAL_TO_LOCAL_CURRENCY_RATE = 1.00;

    /**
     * @return int
     */
    public function getMinimalMargin()
    {
        return (int) Mage::getStoreConfig('catalog/manufacturer/minimal_margin');
    }

    /**
     * @param float $price
     * @return float
     */
    public function convertExternalPriceToLocalPrice($price)
    {
        return round($price * $this->_getExternalToLocalCurrencyRate(), 2);
    }

    /**
     * @return float
     */
    private function _getExternalToLocalCurrencyRate()
    {
        $rate = (float) Mage::getStoreConfig('catalog/manufacturer/gbp_to_base_currency_rate');

        if ($rate > 0) {
            return $rate;
        }

        return self::DEFAULT_EXTERNAL_TO_LOCAL_CURRENCY_RATE;
    }
}
