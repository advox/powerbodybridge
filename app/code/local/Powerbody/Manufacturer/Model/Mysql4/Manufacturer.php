<?php

/**
 * Class Powerbody_Manufacturer_Model_Mysql4_Manufacturer
 */
class Powerbody_Manufacturer_Model_Mysql4_Manufacturer 
    extends Mage_Core_Model_Mysql4_Abstract 
{
    
    /**
     * construct
     */
    protected function _construct() 
    {
        $this->_init('manufacturer/manufacturer', 'id');
    }
    
    /**
     * 
     * @param Mage_Core_Model_Abstract $object
     * @return Powerbody_Manufacturer_Model_Mysql4_Manufacturer
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $oldStores = $this->_lookupStoreIds($object->getId());
        $newStores = (array) $object->getStoreIds();

        $table  = $this->getTable('manufacturer/manufacturer_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if (count($delete) > 0) {
            $where = array(
                'manufacturer_id = ?'   => (int) $object->getId(),
                'store_id IN (?)'       => $delete,
            );
            $this->_getWriteAdapter()->delete($table, $where);
        }

        if (count($insert) > 0) {
            $data = array();
            foreach ($insert as $storeId) {
                $data[] = array(
                    'manufacturer_id'   => (int) $object->getId(),
                    'store_id'          => (int) $storeId,
                    'url_key'           => Mage::helper('manufacturer/url')->generateUrl($object, $storeId),
                );
            }
            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }

        return parent::_afterSave($object);

    }
    
    /**
     * @param Mage_Core_Model_Abstract $object
     * @return Powerbody_Manufacturer_Model_Mysql4_Manufacturer
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if (null !== $object->getId()) {
            $stores = $this->_lookupStoreIds($object->getId());
            $object->setData('store_ids', $stores);
        }

        return parent::_afterLoad($object);
    }
    
    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Core_Model_Abstract $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $storeId = $object->getData('filter_by_store_id');
        if (true !== empty($storeId)) {
            $select
                ->joinLeft(
                    array(
                        'ms' => $this->getTable('manufacturer/manufacturer_store')
                    ),
                    'manufacturer_id = ms.manufacturer_id AND ms.store_id = ' . $storeId,
                    array(
                        'url_key'
                    )
                );
        }
        return $select;
    }
    
    /**
     * @param int $id
     * @return array
     */
    protected function _lookupStoreIds($id)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getTable('manufacturer/manufacturer_store'), 'store_id')
            ->where('manufacturer_id = :manufacturer_id');

        $binds = array(
            ':manufacturer_id' => (int) $id
        );

        return $adapter->fetchCol($select, $binds);
    }
    
    /**
     * Prepare data for save
     * @param Mage_Core_Model_Abstract $object
     * @return array
     */
    protected function _prepareDataForSave(Mage_Core_Model_Abstract $object)
    {
        $currentTime = date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time()));
        if ((null === $object->getId() || true === $object->isObjectNew()) && null === $object->getData('created_date')) {
            $object->setData('created_date', $currentTime);
        }
        $object->setData('updated_date', $currentTime);
        return parent::_prepareDataForSave($object);
    }
    
}
