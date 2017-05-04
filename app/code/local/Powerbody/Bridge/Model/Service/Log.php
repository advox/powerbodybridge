<?php

/**
 * Class Powerbody_Bridge_Model_Service_Log
 */
class Powerbody_Bridge_Model_Service_Log extends Mage_Core_Model_Abstract
{
    const STATUS_SUCCESS = 1;
    const STATUS_FAIL = 0;
    const STATUS_SUCCESS_TEXT = 'SUCCESS';
    const STATUS_FAIL_TEXT = 'FAIL';

    protected function _construct()
    {
        $this->_init('bridge/service_log');
    }
}
