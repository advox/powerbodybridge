<?php

/**
 * Class Powerbody_Manufacturer_Model_Provider_Manufacturer
 */
class Powerbody_Manufacturer_Model_Provider_Manufacturer
{
    
    /**
     * @var Powerbody_Manufacturer_Model_Mysql4_Manufacturer_Collection
     */
    protected $_storeManufacturersCollection = null;
    
    /**
     * @param int|string $margin
     * @return Powerbody_Manufacturer_Model_Mysql4_Manufacturer_Collection
     */
    public function getManufacturerCollectionWithMarginLowerThan($margin)
    {
        return Mage::getModel('manufacturer/manufacturer')
            ->getCollection()
            ->addFieldToFilter('margin', ['lt' => $margin]);
    }

    /**
     * @param int $id
     * @return Powerbody_Manufacturer_Model_Manufacturer
     */
    public function loadById($id)
    {
        return Mage::getModel('manufacturer/manufacturer')->load($id);
    }

    /**
     * @param string $direction
     * @return Powerbody_Manufacturer_Model_Mysql4_Manufacturer_Collection
     */
    public function getCollectionSortByName($direction = Varien_Data_Collection::SORT_ORDER_ASC)
    {
        return Mage::getModel('manufacturer/manufacturer')
            ->getCollection()
            ->setOrder('name', $direction);
    }
    
    /**
     *
     * @param string $urlKey
     * @return int
     */
    public function getManufacturerIdByUrlKey($urlKey)
    {
        $manufacturerStore = Mage::getModel('manufacturer/manufacturer_store')
            ->getCollection()
            ->addFieldToFilter('url_key', $urlKey)
            ->addFieldToFilter('store_id', Mage::app()->getStore()->getId())
            ->getFirstItem();
        /* @var $manufacturerStore Powerbody_Manufacturer_Model_Manufacturer_Store */
        return $manufacturerStore->getManufacturerId();
    }
    
    /**
     * @param int $id
     * @param int $storeId
     * @return Powerbody_Manufacturer_Model_Manufacturer
     */
    public function getStoreManufacturerById($id, $storeId)
    {
        return Mage::getModel('manufacturer/manufacturer')
            ->addStoreFilter($storeId)
            ->load($id);
    }
    
    /**
     * @return Powerbody_Manufacturer_Model_Mysql4_Manufacturer_Collection
     */
    public function getStoreManufacturersCollection()
    {
        if (null === $this->_storeManufacturersCollection) {
            $this->_storeManufacturersCollection = Mage::getModel('manufacturer/manufacturer')
                ->getCollection()
                ->setOrder('priority', Varien_Data_Collection::SORT_ORDER_DESC);
        }
        return $this->_storeManufacturersCollection;
    }
    
    /**
     * @param int $productId
     * @return array
     */
    public function getProductManufacturerIds($productId)
    {
        return $this->_getProductManufacturerIds($productId);
    }

    /**
     * @return array
     */
    public function getManufacturersIds()
    {
        $manufacturersCollection = $this->getCollection();

        return $manufacturersCollection->getColumnValues('id');
    }

    /**
     * @return Powerbody_Manufacturer_Model_Mysql4_Manufacturer_Collection
     */
    public function getCollection()
    {
        return Mage::getModel('manufacturer/manufacturer')
            ->getCollection();
    }

    /**
     * @param array $manufacturerIds
     * @return array
     */
    public function getExistentManufacturersIds(array $manufacturerIds)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->_getCoreResource()->getTableName('manufacturer'))
            ->where('id IN (?)', $manufacturerIds)
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(['id']);

        return $this->_getReadAdapter()->fetchAll($select);
    }

    /**
     * @param int $productId
     * @return array
     */
    protected function _getProductManufacturerIds($productId)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from(
                $this->_getCoreResource()->getTableName('manufacturer/product'),
                'manufacturer_id'
            )
            ->where('product_id = :product_id');

        $binds = array(
            ':product_id' => (int) $productId
        );

        return $adapter->fetchCol($select, $binds);
    }

    /**
     * @return Magento_Db_Adapter_Pdo_Mysql
     */
    protected function _getReadAdapter()
    {
        return $this->_getCoreResource()->getConnection('core_read');
    }

    /**
     * @return Mage_Core_Model_Resource
     */
    private function _getCoreResource()
    {
        return Mage::getSingleton('core/resource');
    }
}
