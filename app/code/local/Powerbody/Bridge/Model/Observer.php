<?php

/**
 * Class Powerbody_Bridge_Model_Observer
 */
class Powerbody_Bridge_Model_Observer
{
    /**
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function addOrderToBridgeOrderTransferStatus(Varien_Event_Observer $observer)
    {
        /* @var $event Varien_Event */
        $event = $observer->getEvent();
        /* @var $order Mage_Sales_Model_Order */
        $order = $event->getData('order');
        $bridgeOrderTransferStatusModel = Mage::getModel('bridge/order_transfer_status');
        $bridgeOrderTransferStatusModel->addData(
            [
                'order_id'  => $order->getId(),
                'status'    => Powerbody_Bridge_Model_Order_Transfer_Status::STATUS_NOT_PUSH,
            ]
        );
        $bridgeOrderTransferStatusModel->save();

        return $this;
    }
}
