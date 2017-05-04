<?php

/**
 * Class Powerbody_Bridge_Model_Mysql4_Service_Log_Collection
 */
class Powerbody_Bridge_Model_Mysql4_Service_Log_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('bridge/service_log');
    }
}
