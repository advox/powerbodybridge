<?php

/**
 * Class Powerbody_Bridge_Model_Product_Attribute_Loader
 */
class Powerbody_Bridge_Model_Product_Attribute_Loader
{
    /**
     * @param $code
     *
     * @return Mage_Eav_Model_Entity_Attribute
     */
    public function loadAttribute($code)
    {
        return Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $code);
    }

    /**
     * @param array $codes
     *
     * @return Mage_Eav_Model_Entity_Attribute[]
     */
    public function loadAttributes(array $codes)
    {
        $attributes = [];
        foreach ($codes as $code) {
            $attributes[$code] = $this->loadAttribute($code);
        }

        return $attributes;
    }
}
