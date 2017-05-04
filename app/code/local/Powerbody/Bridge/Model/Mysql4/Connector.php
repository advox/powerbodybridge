<?php

/**
 * Class Powerbody_Bridge_Model_Mysql4_Connector
 */
class Powerbody_Bridge_Model_Mysql4_Connector extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('bridge/connector', 'id');
    }
}
