<?php

/**
 * Class Powerbody_Bridge_Model_Service_Order_Status_Updater
 */
class Powerbody_Bridge_Model_Service_Order_Status_Updater
{
    /**
     * @param Mage_Sales_Model_Order $orderModel
     * @param string                 $status
     * @param string                 $state
     */
    public function updateStatusAndState(Mage_Sales_Model_Order $orderModel, $status, $state)
    {
        if (false === $this->_isStatusValid($status)) {
            $status = Mage_Sales_Model_Order::STATE_PROCESSING;
        }

        if ($orderModel->getData('status') === $status && $orderModel->getData('state') === $state) {
            return;
        }

        $orderModel
            ->setData('status', $status)
            ->setData('state', $state)
            ->save();
    }

    /**
     * @param string $status
     *
     * @return bool
     */
    private function _isStatusValid($status)
    {
        /* @var $statusModel Mage_Sales_Model_Order_Status */
        $statusModel = Mage::getModel('sales/order_status')->load($status, 'status');

        return $statusModel->getId() !== null;
    }
}
