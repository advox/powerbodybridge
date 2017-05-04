<?php

/**
 * Class Powerbody_Bridge_Model_Mysql4_Order_Pushed_Collection
 */
class Powerbody_Bridge_Model_Mysql4_Order_Pushed_Collection
    extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('bridge/order_pushed');
    }
}
