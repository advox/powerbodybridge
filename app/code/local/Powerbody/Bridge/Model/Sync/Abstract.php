<?php

/**
 * Class Powerbody_Bridge_Model_Sync_Abstract
 */
abstract class Powerbody_Bridge_Model_Sync_Abstract
{
    /** @var Powerbody_Bridge_Model_Service */
    protected $_service;

    /** @var Varien_Db_Adapter_Interface */
    protected $_connection;

    abstract public function processImport();

    public function __construct()
    {
        $this->_service = Mage::getSingleton('bridge/service');
        $this->_connection = Mage::getSingleton('core/resource')->getConnection('core_write');
    }

    /**
     * @param array $itemsDataArray
     *
     * @return bool
     */
    protected function _checkResponseArrayIsValid(array $itemsDataArray)
    {
        return (true === is_array($itemsDataArray)
            && true === isset($itemsDataArray['success'])
            && true === $itemsDataArray['success']
            && true === isset($itemsDataArray['data'])
            && true === is_array($itemsDataArray['data'])
        );
    }

    /**
     * @param string $serviceMethod
     * @param array  $serviceParams
     *
     * @return array|null
     * @throws \Exception
     */
    final protected function _makeServiceMethodRequest($serviceMethod, array $serviceParams)
    {
        try {
            $this->_service->connect();
            $responseArray = $this->_service->callMethod([
                'json_data'     => $this->_getCoreHelper()->jsonEncode($serviceParams),
                'method_name'   => $serviceMethod,
            ]);
        } catch (\SoapFault $e) {
            $this->_getLog()->logException($e);
            $responseArray = null;
        }

        return $responseArray;
    }

    /**
     * @param string $sku
     *
     * @return Mage_Catalog_Model_Product
     */
    final protected function _getProductModel($sku = null)
    {
        /* @var $productModel Mage_Catalog_Model_Product */
        $productModel = Mage::getModel('catalog/product');
        if ($sku !== null) {
            $productModel->load($productModel->getIdBySku($sku));
        }

        return $productModel;
    }

    /**
     * @return Powerbody_Bridge_Helper_Data
     */
    final protected function _getBridgeHelper()
    {
        return Mage::helper('bridge');
    }

    /**
     * @return Mage_Core_Model_Store
     */
    final protected function _getStoreModel()
    {
        return Mage::getModel('core/store');
    }

    /**
     * @return Mage_Core_Helper_Data
     */
    final protected function _getCoreHelper()
    {
        return Mage::helper('core');
    }

    /**
     * @return Mage_Eav_Model_Config
     */
    final protected function _getEavConfig()
    {
        return Mage::getSingleton('eav/config');
    }

    /**
     * @return Powerbody_Bridge_Model_Service_Logger
     */
    final protected function _getLog()
    {
        return Mage::getModel('bridge/service_logger');
    }

    /**
     * @return Powerbody_Bridge_Model_Provider_Product
     */
    final protected function _getProductProvider()
    {
        return Mage::getSingleton('bridge/provider_product');
    }
}
