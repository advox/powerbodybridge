<?php

/**
 * Class Powerbody_Manufacturer_Model_Source_Manufacturer
 */
class Powerbody_Manufacturer_Model_Source_Manufacturer extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /** @var Powerbody_Manufacturer_Model_Provider_Manufacturer */
    private $_provider;

    /** @var null|Powerbody_Manufacturer_Model_Mysql4_Manufacturer_Collection */
    private $_manufacturerCollection;

    public function __construct()
    {
        $this->_provider = Mage::getModel('manufacturer/provider_manufacturer');
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        $options = [-1 => Mage::helper('manufacturer')->__('-- Select --')];
        foreach ($this->_getManufacturerCollection() as $manufacturerModel) {
            /** @var $manufacturerModel Powerbody_Manufacturer_Model_Manufacturer */
            $options[(string)$manufacturerModel->getId()] = $manufacturerModel->getData('name');
        }

        return $options;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        foreach ($this->_getManufacturerCollection() as $manufacturerModel) {
            /** @var $manufacturerModel Powerbody_Manufacturer_Model_Manufacturer */
            $optionArray[] = [
                'value' => $manufacturerModel->getId(),
                'label' => $manufacturerModel->getData('name'),
            ];
        }

        return $optionArray;
    }

    /**
     * @return Powerbody_Manufacturer_Model_Mysql4_Manufacturer_Collection
     */
    private function _getManufacturerCollection()
    {
        if (!$this->_manufacturerCollection instanceof Powerbody_Manufacturer_Model_Mysql4_Manufacturer_Collection) {
            $this->_manufacturerCollection = $this->_provider->getCollection()->setOrder('name', Varien_Data_Collection::SORT_ORDER_ASC);
        }

        return $this->_manufacturerCollection;
    }
}
