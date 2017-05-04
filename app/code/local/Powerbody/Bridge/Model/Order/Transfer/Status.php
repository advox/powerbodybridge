<?php

/**
 * Class Powerbody_Bridge_Model_Order_Transfer_Status
 */
class Powerbody_Bridge_Model_Order_Transfer_Status extends Mage_Core_Model_Abstract
{
    const STATUS_NOT_PUSH = 0;
    const STATUS_PUSH = 1;
    const STATUS_RESPONSE_FAIL = 2;

    protected function _construct()
    {
        parent::_construct();
        $this->_init('bridge/order_transfer_status');
    }

    /**
     * Before save
     */
    protected function _beforeSave()
    {
        $this->setData('updated_at', Mage::getSingleton('core/date')->gmtDate());

        return parent::_beforeSave();
    }
}
