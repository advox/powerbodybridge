<?php

/**
 * Class Powerbody_Bridge_Model_Service
 */
class Powerbody_Bridge_Model_Service
{
    const PREFIX = 'bridge';

    protected $_apiUserName = null;
    protected $_apiUserPassword = null;
    protected $_wsdl = null;
    protected $_client = null;
    protected $_session = null;

    /**
     * @param array $params
     */
    public function __construct($params = [])
    {
       $this->_setApiUserName(isset($params['api_user_name']) ? $params['api_user_name'] : null);
       $this->_setApiUserPassword(isset($params['api_user_password']) ? $params['api_user_password'] : null);
       $this->_setWsdl(isset($params['wsdl']) ? $params['wsdl'] : null);
    }

    /**
     * Connect
     */
    public function connect()
    {
        if (!$this->isConnected()) {
            $wsdl = $this->_getWsdl();
            if (!empty($wsdl)) {
                $this->_client = new SoapClient($wsdl, array('cache_wsdl' => WSDL_CACHE_NONE));
                $this->_session = $this->_client->login($this->_getApiUserName(), $this->_getApiUserPassword());
            }
        }
    }

    /**
     * Disconnect
     */
    public function disconnect()
    {
        if ($this->_client && $this->_session) {
            $this->_client->endSession($this->_session);
        }
    }

    /**
     * @return bool
     */
    public function isConnected()
    {
        return (bool) $this->_session;
    }
    
    /**
     * @param array $params
     * @param bool $assoc
     *
     * @return array|stdClass
     * @throws Exception
     */
    public function callMethod($params = array(), $assoc = true)
    {
        $jsonData = null;
        $methodName = null;
        $result = null;
        try {
            $methodName = isset($params['method_name']) ? self::PREFIX . '.' . $params['method_name']  : null;
            $jsonData = isset($params['json_data']) ? $params['json_data'] : null;
            $result = json_decode($this->_client->call($this->_session, $methodName, $jsonData), $assoc);
            $serviceLogArrayData = array(
                'api_method_name'   => $methodName,
                'api_user_name'     => $this->_getApiUserName(),
                'params'            => $jsonData,
                'status'            => Powerbody_Bridge_Model_Service_Log::STATUS_SUCCESS
            );
            $this->_insertServiceLogItem($serviceLogArrayData);
        } catch (Exception $e) {
            Mage::logException($e);
            $serviceLogArrayData = array(
                'api_method_name'   => $methodName,
                'api_user_name'     => $this->_getApiUserName(),
                'info'              => $e->getMessage(),
                'params'            => $jsonData,
                'status'            => Powerbody_Bridge_Model_Service_Log::STATUS_FAIL,
            );
            $this->_insertServiceLogItem($serviceLogArrayData);
            throw new Exception($e->getMessage());
            
        }
        return $result;
    }

    /**
     * @return string|null
     *
     * @throws Exception
     */
    protected function _getApiUserName() 
    {
        if (!$this->_apiUserName) {
            $this->_apiUserName = Mage::getStoreConfig('bridge_settings/service_bridge_settings/default_api_user_name');
        }
        if (is_null($this->_apiUserName)) {
            throw new Exception(Mage::helper('bridge')->__('Invalid Configuration API user name'));
        }
        return $this->_apiUserName;
    }
    
    /**
     * @return string|null
     *
     * @throws Exception
     */
    protected function _getApiUserPassword()
    {
        if (!$this->_apiUserPassword) {
            $this->_apiUserPassword = Mage::getStoreConfig('bridge_settings/service_bridge_settings/default_api_user_password');
        }
        if (is_null($this->_apiUserPassword)) {
            throw new Exception(Mage::helper('bridge')->__('Invalid Configuration API User password'));
        }
        return $this->_apiUserPassword;
    }

    /**
     * @return string
     *
     * @throws Exception
     */
    protected function _getWsdl() 
    {
        if (!$this->_wsdl) {
            $this->_wsdl = Mage::getStoreConfig('bridge_settings/service_bridge_settings/default_wsdl_url');
        }
        if (is_null($this->_wsdl)) {
            throw new Exception(Mage::helper('bridge')->__('Invalid Configuration WSDL'));
        }
        return $this->_wsdl;
    }
    
    /**
     * @param array $dataArray
     */
    protected function _insertServiceLogItem($dataArray = array()) 
    {
        $serviceLogModel = Mage::getModel('bridge/service_log');
        $serviceLogModel->setData($dataArray);
        try {
            $serviceLogModel->save();
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::log($e->getMessage());
        }
    }

    /**
     * @param string $apiUserName
     */
    protected function _setApiUserName($apiUserName) 
    {
        $this->_apiUserName = $apiUserName;
    }
    
    /**
     * @param string $apiUserPassword
     */
    protected function _setApiUserPassword($apiUserPassword) 
    {
        $this->_apiUserPassword = $apiUserPassword;
    }
    
    /**
     * @param string $wsdl
     */
    protected function _setWsdl($wsdl) 
    {
        $this->_wsdl = $wsdl;
    }
}
