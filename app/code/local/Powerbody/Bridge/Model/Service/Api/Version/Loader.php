<?php
/**
 * Class Powerbody_Bridge_Model_Service_Api_Version_Loader
 */
class Powerbody_Bridge_Model_Service_Api_Version_Loader
{
    /**
     * @var Mage_Core_Model_Resource
     */
    private $_coreResource;

    /**
     * @var Varien_Db_Adapter_Interface
     */
    private $_connection;

    public function __construct()
    {
        $this->_coreResource = Mage::getModel('core/resource');
        $this->_connection = $this->_coreResource->getConnection('core_read');
    }

    /**
     * @return string
     */
    public function getApiVersion()
    {
        $select = $this->_connection->select()
            ->from($this->_coreResource->getTableName('core_resource'))
            ->where('code = ?', 'bridge_setup')
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns('version');

        return (string)$this->_connection->fetchOne($select);
    }
}