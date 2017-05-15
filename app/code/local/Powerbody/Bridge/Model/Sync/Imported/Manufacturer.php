<?php

/**
 * Class Powerbody_Bridge_Model_Sync_Imported_Manufacturer
 */
class Powerbody_Bridge_Model_Sync_Imported_Manufacturer extends Powerbody_Bridge_Model_Sync_Imported_Abstract
{
    const TABLE_NAME = 'bridge_imported_manufacturer';
    const ID_FIELD = 'base_manufacturer_id';
    const SERVICE_METHOD = 'getManufacturers';

    /**
     * @var array
     */
    private $_currentManufacturerIds = [];
    protected $_manufacturerNotVisibleOnFrontArray = null;

    public function __construct()
    {
        parent::__construct();
        $this->_currentManufacturerIds = $this->_getCurrentManufacturerIds();
        $this->_serviceParams = [self::SERVICE_PARAM_FOR_DROPCLIENT  => true];
    }
    
    /**
     * @param array $itemsDataArray
     */
    protected function _removeIds(array $itemsDataArray)
    {
        $apiCurrentManufacturerIds = $this->_getApiCurrentManufacturerIds($itemsDataArray);
        
        if (true === empty($apiCurrentManufacturerIds)) {
            return;
        }
        $removeIds = array_diff(array_keys($this->_currentManufacturerIds), $apiCurrentManufacturerIds);
        
        if (true === empty($removeIds)) {
            return;
        }
        $this->_connection->update(
            self::TABLE_NAME,
            ['dropshipping_status' => Powerbody_Bridge_Model_Imported_Manufacturer::DROPSHIPPING_STATUS_DISABLED],
            new Zend_Db_Expr(self::ID_FIELD . ' IN  (' . implode(', ', $removeIds) . ')')
        );
        $clientManufacturerIds = $this->_getClientManufacturerIdsToRemove($removeIds);
        
        if (true === empty($clientManufacturerIds)) {
            return;
        }
        $this->_connection->update(
            'manufacturer',
            ['is_visible_on_front' => Powerbody_Bridge_Model_Imported_Manufacturer::DROPSHIPPING_STATUS_DISABLED],
            new Zend_Db_Expr('id IN  (' . implode(', ', $clientManufacturerIds) . ')')
        );
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    protected function _isExistsRecord($id)
    {
        return in_array($id, array_keys($this->_currentManufacturerIds));
    }

    /**
     * @return array
     */
    private function _getCurrentManufacturerIds()
    {
        $stmt = $this->_connection->query(
            new Zend_Db_Expr(
                'SELECT base_manufacturer_id, client_manufacturer_id FROM ' . self::TABLE_NAME 
            )
        );

        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    /**
     * @param array $itemsDataArray
     *
     * @return array
     */
    private function _getApiCurrentManufacturerIds($itemsDataArray)
    {
        if (true === empty($itemsDataArray)) {
            return [];
        }

        return array_column($itemsDataArray, self::ID_FIELD);
    }

    /**
     * @param array $removeIds
     *
     * @return array
     */
    private function _getClientManufacturerIdsToRemove(array $removeIds)
    {
        $clientManufacturerIds = [];

        foreach ($removeIds as $id) {
            if (true === isset($this->_currentManufacturerIds[$id])) {
                $clientManufacturerIds[] = $this->_currentManufacturerIds[$id];
            }
        }

        return $clientManufacturerIds;
    }
    
    /**
     * 
     * @param int $id
     * @param array $itemData
     */
    protected function _saveItem($id, $itemData) 
    {
        $itemData['dropshipping_status'] =  Powerbody_Bridge_Model_Imported_Manufacturer::DROPSHIPPING_STATUS_ENABLED;
        if (isset($itemData['base_manufacturer_id'])) {
            
            $this->_updateNotVisibleOnFrontManufacturer($itemData['base_manufacturer_id']);
        }
        
        parent::_saveItem($id, $itemData);
    }
    
    /**
     * @param int $baseManufacturerId
     */
    protected function _updateNotVisibleOnFrontManufacturer($baseManufacturerId)
    {
        $manufacturerModel = $this->_getManufacturerModelNotVisibleOnFrontByBaseId($baseManufacturerId);
        
        if ($manufacturerModel instanceof Powerbody_Manufacturer_Model_Manufacturer && $manufacturerModel->getId() !== null) {
            $manufacturerModel->setData('is_visible_on_front', 1);
            $manufacturerModel->save();
        }
    }
    
    /**
     * @param type $baseManufacturerId
     * @return boolean|mixed
     */
    protected function _getManufacturerModelNotVisibleOnFrontByBaseId($baseManufacturerId)
    {
        $manufacturerNotVisibleOnFrontArray = $this->_getManufacturerNotVisibleOnFrontArray();
        
        if (isset($manufacturerNotVisibleOnFrontArray[$baseManufacturerId])) {
            return $manufacturerNotVisibleOnFrontArray[$baseManufacturerId];
        }
        
        return null;
    }
    
    /**
     * @return array
     */
    protected function _getManufacturerNotVisibleOnFrontArray()
    {
        if (null === $this->_manufacturerNotVisibleOnFrontArray) {
            $notVisibleManufacturerCollection = Mage::getModel('manufacturer/manufacturer')
                ->getCollection();
            $notVisibleManufacturerCollection->getSelect()->joinLeft(
                ['bim' => Mage::getSingleton('core/resource')->getTableName('bridge/imported_manufacturer')],
                'main_table.id = bim.client_manufacturer_id',
                ['base_manufacturer_id' => 'bim.base_manufacturer_id']
            );
            $notVisibleManufacturerCollection->getSelect()->where('bim.client_manufacturer_id IS NOT NULL AND main_table.is_visible_on_front = 0');
            $manufacturerNotVisibleOnFrontArray = [];
        
            foreach ($notVisibleManufacturerCollection as $manufacturerModel) {
                /* @var $manufacturerModel Powerbody_Manufacturer_Model_Manufacturer */
                $manufacturerNotVisibleOnFrontArray[$manufacturerModel->getData('base_manufacturer_id')] = $manufacturerModel;
            }
            $this->_manufacturerNotVisibleOnFrontArray = $manufacturerNotVisibleOnFrontArray;
        }
        
        return $this->_manufacturerNotVisibleOnFrontArray;
    }
}
