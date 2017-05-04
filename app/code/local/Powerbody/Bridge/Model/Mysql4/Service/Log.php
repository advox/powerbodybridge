<?php

/**
 * Class Powerbody_Bridge_Model_Mysql4_Service_Log
 */
class Powerbody_Bridge_Model_Mysql4_Service_Log extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('bridge/service_log', 'id');
    }
}
