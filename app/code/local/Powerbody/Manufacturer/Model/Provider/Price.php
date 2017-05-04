<?php

/**
 * Class Powerbody_Manufacturer_Model_Provider_Price
 */
class Powerbody_Manufacturer_Model_Provider_Price
{
    /**
     * @var array
     */
    private $_loadedMargins = [];

    /**
     * @var Mage_Core_Model_Resource
     */
    private $_coreResource;

    /**
     * @var Varien_Db_Adapter_Interface
     */
    private $_DAO;

    public function __construct()
    {
        $this->_coreResource = Mage::getModel('core/resource');
        $this->_DAO = $this->_coreResource->getConnection('core_write');
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param $price
     * @param array $marginArray
     * @return float
     */
    public function calculateMarginForProduct(Mage_Catalog_Model_Product $product, $price, array $marginArray)
    {
        $productId = (int) $product->getId();
        $margin = 0;
        if (isset($marginArray[$productId])) {
            $margin = $marginArray[$productId];
        }
        $marginMultiplier = $margin / 100;

        $price = $price * (1 + $marginMultiplier);
        return round($price, 2);
    }

    public function getMarginForProducts()
    {
        $select = $this->_DAO->select()
            ->from(['mp' => $this->_coreResource->getTableName('manufacturer_product')])
            ->join(
                ['m' => $this->_coreResource->getTableName('manufacturer')],
                'm.id=mp.manufacturer_id'
            )
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(['mp.product_id', 'm.margin']);

        return $this->_DAO->fetchPairs($select);
    }
}
