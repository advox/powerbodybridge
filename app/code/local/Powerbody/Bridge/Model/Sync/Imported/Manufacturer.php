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

        $this->_connection->delete(
            self::TABLE_NAME,
            new Zend_Db_Expr(self::ID_FIELD . ' IN  (' . implode(', ', $removeIds) . ')')
        );

        $clientManufacturerIds = $this->_getClientManufacturerIdsToRemove($removeIds);
        if (true === empty($clientManufacturerIds)) {
            return;
        }

        $this->_connection->delete(
            'manufacturer',
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
}
