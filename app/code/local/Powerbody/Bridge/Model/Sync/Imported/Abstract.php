<?php

/**
 * Class Powerbody_Bridge_Model_Sync_Imported_Abstract
 */
abstract class Powerbody_Bridge_Model_Sync_Imported_Abstract extends Powerbody_Bridge_Model_Sync_Abstract
{
    const SERVICE_PARAM_FOR_DROPCLIENT = 'for_dropclient';

    /**
     * @var Varien_Db_Adapter_Interface
     */
    protected $_connection;

    /**
     * @var array
     */
    protected $_serviceParams = [];

    public function __construct()
    {
        parent::__construct();
        $this->_connection = $this->_getConnection();
    }

    public function processImport()
    {
        $itemsDataArray = $this->_getItemsDataArray();
        if (true === empty($itemsDataArray)) {
            return;
        }
        $this->saveItems($itemsDataArray);
    }

    /**
     * @param array $itemsDataArray
     */
    abstract protected function _removeIds(array $itemsDataArray);

    /**
     * @param int $id
     *
     * @return bool
     */
    abstract protected function _isExistsRecord($id);

    /**
     * @param array $itemsDataArray
     */
    public function saveItems(array $itemsDataArray)
    {
        $this->_connection->beginTransaction();
        try {
            $this->_update($itemsDataArray);
            $this->_removeIds($itemsDataArray);

            $this->_connection->commit();
        } catch (Exception $e) {
            $this->_connection->rollBack();
            $this->_getLog()->logException($e);
        }
    }

    /**
     * @param array $itemsDataArray
     */
    protected function _update($itemsDataArray)
    {
        foreach ($itemsDataArray as $itemData) {
            if (false === isset($itemData[static::ID_FIELD])) {
                continue;
            }
            $baseId = $itemData[static::ID_FIELD];
            $this->_saveItem($baseId, $itemData);
        }
    }

    /**
     * @param int   $id
     * @param array $itemData
     */
    protected function _saveItem($id, $itemData)
    {
        $updatedDate = Mage::getSingleton('core/date')->gmtDate();

        if (true === $this->_isExistsRecord($id)) {
            $itemData['updated_date'] = $updatedDate;

            $this->_connection->update(
                static::TABLE_NAME,
                $itemData,
                new Zend_Db_Expr(static::ID_FIELD . ' = ' . $id)
            );
        } else {
            $itemData['created_date'] = $updatedDate;
            $itemData['updated_date'] = $updatedDate;

            $this->_connection->insert(static::TABLE_NAME, $itemData);
        }
    }

    /**
     * @return Varien_Db_Adapter_Interface
     */
    protected function _getConnection()
    {
        /* @var $resourceModel Mage_Core_Model_Resource */
        $resourceModel = Mage::getSingleton('core/resource');
        return $resourceModel->getConnection('core_write');
    }

    /**
     * @return array
     *
     * @throws Exception
     */
    protected function _getItemsDataArray()
    {
        $dataArray = [];
        $this->_service->connect();
        try {
            $dataArray = $this->_service->callMethod([
                'json_data'     => $this->_getCoreHelper()->jsonEncode($this->_serviceParams),
                'method_name'   => static::SERVICE_METHOD
            ]);
        } catch (Exception $e) {
            $this->_getLog()->logException($e);
        }

        return $dataArray;
    }
}
