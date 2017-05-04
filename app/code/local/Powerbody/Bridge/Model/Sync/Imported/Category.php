<?php

/**
 * Class Powerbody_Bridge_Model_Sync_Imported_Category
 */
class Powerbody_Bridge_Model_Sync_Imported_Category extends Powerbody_Bridge_Model_Sync_Imported_Abstract
{
    const TABLE_NAME = 'bridge_imported_category';
    const SERVICE_PARAM_LOCALE = 'locale';
    const ID_FIELD = 'base_category_id';
    const SERVICE_METHOD = 'getAllCategories';

    /**
     * @var array
     */
    private $_currentCategoryIds = [];

    public function __construct()
    {
        parent::__construct();

        $this->_currentCategoryIds = $this->_getCurrentCategoryIds();
        $this->_serviceParams = [
            self::SERVICE_PARAM_LOCALE          => $this->_getLocaleCode(),
            self::SERVICE_PARAM_FOR_DROPCLIENT  => true,
        ];
    }

    /**
     * @param array $itemsDataArray
     */
    protected function _removeIds(array $itemsDataArray)
    {
        $apiCurrentCategoryIds = $this->_getApiCurrentCategoryIds($itemsDataArray);
        if (true === empty($apiCurrentCategoryIds)) {
            return;
        }

        $removeIds = array_diff(array_keys($this->_currentCategoryIds), $apiCurrentCategoryIds);
        if (true === empty($removeIds)) {
            return;
        }

        $this->_connection->delete(
            self::TABLE_NAME,
            new Zend_Db_Expr(self::ID_FIELD . ' IN  (' . implode(', ', $removeIds) . ')')
        );

        $clientCategoryIds = $this->_getClientCategoryIdsToRemove($removeIds);
        if (true === empty($clientCategoryIds)) {
            return;
        }

        $this->_connection->delete(
            'catalog_category_entity',
            new Zend_Db_Expr('entity_id IN  (' . implode(', ', $clientCategoryIds) . ')')
        );
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    protected function _isExistsRecord($id)
    {
        return in_array($id, array_keys($this->_currentCategoryIds));
    }

    /**
     * @param array $removeIds
     *
     * @return array
     */
    private function _getClientCategoryIdsToRemove(array $removeIds)
    {
        $clientCategoryIds = [];

        foreach ($removeIds as $id) {
            if (true === isset($this->_currentCategoryIds[$id])) {
                $clientCategoryIds[] = $this->_currentCategoryIds[$id];
            }
        }

        return $clientCategoryIds;
    }

    /**
     * @return array
     */
    private function _getCurrentCategoryIds()
    {
        $stmt = $this->_connection->query(
            new Zend_Db_Expr(
                'SELECT base_category_id, client_category_id FROM ' . self::TABLE_NAME
            )
        );

        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    /**
     * @param array $itemsDataArray
     *
     * @return array
     */
    private function _getApiCurrentCategoryIds(array $itemsDataArray)
    {
        if (true === empty($itemsDataArray)) {
            return [];
        }

        return array_column($itemsDataArray, self::ID_FIELD);
    }

    /**
     * @return string
     */
    private function _getLocaleCode()
    {
        return Mage::app()->getLocale()->getLocaleCode();
    }
}
