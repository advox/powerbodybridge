<?php

/**
 * Class Powerbody_Bridge_Model_Service_Logger
 */
class Powerbody_Bridge_Model_Service_Logger
{
    /**
     * @param Exception $exception
     */
    public function logException(Exception $exception)
    {
        if (Mage::getIsDeveloperMode() === true) {
            Mage::logException($exception);
        }

        $log = $this->_getLogObject();

        $this->_setLogData($log, $exception);

        $log->save();
    }

    /**
     * @return Powerbody_Bridge_Model_Sync_Log
     */
    private function _getLogObject()
    {
        return Mage::getModel('bridge/sync_log');
    }

    /**
     * @param Powerbody_Bridge_Model_Sync_Log $log
     * @param Exception $exception
     */
    private function _setLogData(Powerbody_Bridge_Model_Sync_Log $log, Exception $exception)
    {
        $log->setData([
            'code'      => $exception->getCode(),
            'action'    => $exception->getFile() . '::' . $exception->getLine(),
            'message'   => $exception->getMessage(),
            'trace'     => $exception->getTraceAsString(),
        ]);
    }
}
