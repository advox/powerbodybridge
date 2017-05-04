<?php

/**
 * Class Powerbody_Manufacturer_Model_Provider_Catalog_Product_Superattribute
 */
class Powerbody_Manufacturer_Model_Provider_Catalog_Product_Superattribute
{
    /**
     * @var Mage_Core_Model_Resource
     */
    private $_coreResource;

    /**
     * @var Varien_Db_Adapter_Interface
     */
    private $_connection;

    public function __construct()
    {
        $this->_coreResource = Mage::getModel('core/resource');
        $this->_connection = $this->_coreResource->getConnection('core_write');
    }

    /**
     * @param Powerbody_Manufacturer_Model_Manufacturer $manufacturerModel
     * @return array
     */
    public function getSuperAttributes(Powerbody_Manufacturer_Model_Manufacturer $manufacturerModel)
    {
        $select = $this->_getSuperAttributes();
        $this->_joinSuperAttributePricing($select);
        $this->_joinProductSuperLink($select);
        $this->_joinManufacturerProduct($select);
        $this->_filterByManufacturer($select, $manufacturerModel);
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns([
                'cpsa.product_super_attribute_id',
                'cpsa.product_id as configurable_product_id',
                'cpsl.product_id as simple_product_id',
                'cpsap.value_index',
                'cpsap.pricing_value',
            ]);

        return $this->_connection->fetchAll($select);
    }

    /**
     * @return Varien_Db_Select
     */
    private function _getSuperAttributes()
    {
        $select = $this->_connection->select()
            ->from([
                'cpsa' => $this->_coreResource->getTableName('catalog_product_super_attribute')
            ]);

        return $select;
    }

    /**
     * @param Varien_Db_Select $select
     */
    private function _joinSuperAttributePricing(Varien_Db_Select $select)
    {
        $select->join(
            ['cpsap' => $this->_coreResource->getTableName('catalog_product_super_attribute_pricing')],
            'cpsa.product_super_attribute_id=cpsap.product_super_attribute_id'
        );
    }

    /**
     * @param Varien_Db_Select $select
     */
    private function _joinProductSuperLink(Varien_Db_Select $select)
    {
        $select->join(
            ['cpsl' => $this->_coreResource->getTableName('catalog_product_super_link')],
            'cpsa.product_id=cpsl.parent_id'
        );
    }

    /**
     * @param Varien_Db_Select $select
     */
    private function _joinManufacturerProduct(Varien_Db_Select $select)
    {
        $select->join(
            ['mp' => $this->_coreResource->getTableName('manufacturer_product')],
            'mp.product_id=cpsl.product_id'
        );
    }

    /**
     * @param Varien_Db_Select $select
     * @param Powerbody_Manufacturer_Model_Manufacturer $manufacturerModel
     */
    private function _filterByManufacturer(
        Varien_Db_Select $select,
        Powerbody_Manufacturer_Model_Manufacturer $manufacturerModel
    ) {
        $select->where('cpsap.`pricing_value` IS NOT NULL')
            ->where('mp.manufacturer_id = ?', $manufacturerModel->getId());
    }
}
