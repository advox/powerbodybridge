<?php

/**
 * Class Dropshipping_Api
 */
class Dropshipping_Api
{
    /* @var null|string */
    protected $_apiUserName = null;
    
    /* @var null|string */
    protected $_apiKey = null;
    
    /* @var null|string */
    protected $_apiWsdl = null;
    
    /**
     * @param array $params
     */
    public function __construct($params = [])
    {
        if (true === isset($params['api_user_name'])) {
            $this->setApiUserName($params['api_user_name']);
        }

        if (true === isset($params['api_key'])) {
            $this->setApiKey($params['api_key']);
        }

        if (true === isset($params['api_wsdl'])) {
            $this->setApiWsdl($params['api_wsdl']);
        }
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->_apiKey = $apiKey;
    }

    /**
     * @param string $apiUserName
     */
    public function setApiUserName($apiUserName)
    {
        $this->_apiUserName = $apiUserName;
    }

    /**
     * @param string $wsdl
     */
    public function setApiWsdl($wsdl)
    {
        $this->_apiWsdl = $wsdl;
    }

    /**
     * @param array $requestData
     *
     * @return array
     */
    public function createOrder($requestData = [])
    {
        return $this->_makeRequest('dropshipping.createOrder', $requestData);
    }

    /**
     * @param array $requestData
     *
     * @return array
     */
    public function getOrdersForDropclient($requestData = [])
    {
        return $this->_makeRequest('dropshipping.getOrdersForDropclient', $requestData);
    }

    /**
     * @param string     $methodName
     * @param array|null $inputData
     *
     * @return array
     *
     * @throws Exception
     */
    public function _makeRequest($methodName, array $inputData = null)
    {
        $result = null;
        $jsonData = null;

        try {
            if (null !== $inputData) {
                $jsonData = Mage::helper('core')->jsonEncode($inputData);
            }

            $client = new SoapClient($this->_apiWsdl, ['cache_wsdl' => WSDL_CACHE_NONE]);
            $session = $client->login($this->_apiUserName, $this->_apiKey);

            if (null !== $jsonData) {
                $result = $client->call($session, $methodName, $jsonData);
            } else {
                $result = $client->call($session, $methodName);
            }

            $client->endSession($session);
        } catch (Exception $e) {
            throw $e;
        }

        if (true === is_array($result)) {
            return $result;
        }

        return Mage::helper('core')->jsonDecode($result);
    }
}
