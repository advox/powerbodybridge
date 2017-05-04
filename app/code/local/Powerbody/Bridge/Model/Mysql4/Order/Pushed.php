<?php

/**
 * Class Powerbody_Bridge_Model_Mysql4_Order_Pushed
 */
class Powerbody_Bridge_Model_Mysql4_Order_Pushed extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('bridge/order_pushed', 'id');
    }
}
