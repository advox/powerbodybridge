<?php

/**
 * Class Powerbody_Manufacturer_Model_Provider_Catalog_Product_Price
 */
class Powerbody_Manufacturer_Model_Provider_Catalog_Product_Price
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
     * @param array $productIds
     * @return string
     */
    public function getProductsPrices(array $productIds)
    {
        $currentSimplePriceSelect = $this->_connection->select()
            ->from($this->_coreResource->getTableName('catalog_product_entity_decimal'))
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(['entity_id', 'value'])
            ->where('entity_id IN (?)', $productIds);

        return $this->_connection->fetchPairs($currentSimplePriceSelect);
    }
}
