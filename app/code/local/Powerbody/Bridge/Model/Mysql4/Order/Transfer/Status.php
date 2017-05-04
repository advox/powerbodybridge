<?php

/**
 * Class Powerbody_Bridge_Model_Mysql4_Order_Transfer_Status
 */
class Powerbody_Bridge_Model_Mysql4_Order_Transfer_Status extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct() 
    {                
        $this->_init('bridge/order_transfer_status', 'id');
    }
}
