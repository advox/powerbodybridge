<?php

/**
 * Class Powerbody_Manufacturer_Model_Mysql4_Manufacturer_Collection
 */
class Powerbody_Manufacturer_Model_Mysql4_Manufacturer_Collection 
    extends Mage_Core_Model_Mysql4_Collection_Abstract 
{
    
    /**
     * construct
     */
    protected function _construct()
    {
        $this->_init('manufacturer/manufacturer');
    }
    
    /**
     * Add store filter to manufacturer collection
     * @param int|Mage_Core_Model_Store $store
     * @return \Powerbody_Manufacturer_Model_Mysql4_Manufacturer_Collection
     */
    public function addStoreFilter($store = null)
    {
        $storeId = 0;
        if (null === $store) {
            $storeId = Mage::app()->getStore()->getStoreId();
        } else {
            if (true === ($store instanceof Mage_Core_Model_Store)) {
                $storeId = $store->getId();
            } elseif (true == is_numeric($store)) {
                $storeId = $store;
            }
        }
        $this->getSelect()
            ->joinLeft(
                array(
                    'ms' => $this->getTable('manufacturer_store')
                ),
                'main_table.id = ms.manufacturer_id',
                array(
                    'url_key' => 'ms.url_key'
                )
            )
            ->where('ms.store_id = ?', $storeId);
        return $this;
    }
    
}
