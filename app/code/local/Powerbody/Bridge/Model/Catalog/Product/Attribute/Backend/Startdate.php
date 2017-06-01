<?php

class Powerbody_Bridge_Model_Catalog_Product_Attribute_Backend_Startdate extends Mage_Catalog_Model_Product_Attribute_Backend_Startdate
{
    /**
     * Get attribute value for save.
     *
     * @param Varien_Object $object
     * @return string|bool
     */
    protected function _getValueForSave($object)
    {
        $attributeName = $this->getAttribute()->getName();
        $startDate = $object->getData($attributeName);
        if ($startDate === false) {
            return false;
        }

        if ($startDate === '' && $object->getSpecialPrice() && $attributeName !== 'news_from_date') {
            $startDate = Mage::app()->getLocale()->date();
        }

        return $startDate;
    }
}
