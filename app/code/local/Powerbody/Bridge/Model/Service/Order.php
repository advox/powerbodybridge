<?php

/**
 * Class Powerbody_Bridge_Model_Service_Order
 */
class Powerbody_Bridge_Model_Service_Order
{
    /**
     * @param array $ordersData
     */
    public function updateOrdersProcess(array $ordersData)
    {
        /* @var Zend_Db_Adapter_Abstract $connection */
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

        foreach ($ordersData as $orderData) {
            $connection->beginTransaction();
            try {
                $this->_updateOrder($orderData);
                $connection->commit();
            } catch (Exception $e) {
                $connection->rollBack();
                Mage::logException($e);
            }
        }
    }

    /**
     * @param array $orderData
     */
    private function _updateOrder(array $orderData)
    {
        /* @var $orderModel Mage_Sales_Model_Order */
        $orderModel = $this->_getOrderModelById($orderData['order_id']);

        if (null === $orderModel->getId()) {
            return;
        }

        $this->_updateOrderStatusAndState($orderModel, $orderData);
        $this->_updateTrackingNumber($orderModel, $orderData);
    }

    /**
     * @param int $orderId
     *
     * @return Mage_Sales_Model_Order
     */
    private function _getOrderModelById($orderId)
    {
        /* @var $orderProvider Powerbody_Bridge_Model_Provider_Order */
        $orderProvider = Mage::getSingleton('bridge/provider_order');

        return $orderProvider->getOrderById($orderId);
    }

    /**
     * @param Mage_Sales_Model_Order $orderModel
     * @param array                  $orderData
     */
    private function _updateOrderStatusAndState(Mage_Sales_Model_Order $orderModel, array $orderData)
    {
        if (false === isset($orderData['status']) && false === isset($orderData['state'])) {
            return;
        }

        /* @var $updaterService Powerbody_Bridge_Model_Service_Order_Status_Updater */
        $updaterService = Mage::getSingleton('bridge/service_order_status_updater');
        $updaterService->updateStatusAndState($orderModel, $orderData['status'], $orderData['state']);
    }

    /**
     * @param Mage_Sales_Model_Order $orderModel
     * @param array                  $orderData
     */
    private function _updateTrackingNumber(Mage_Sales_Model_Order $orderModel, array $orderData)
    {
        if (false === isset($orderData['tracking_number'])) {
            return;
        }

        $trackNumber = $orderData['tracking_number'];

        /* @var $shipmentTrackModel Mage_Sales_Model_Order_Shipment_Track */
        $shipmentTrackModel = Mage::getModel('sales/order_shipment_track')
            ->getCollection()
            ->addFieldToFilter('track_number', $trackNumber)
            ->addFieldToFilter('order_id', $orderModel->getId())
            ->getFirstItem();

        if (null !== $shipmentTrackModel->getId()) {
            return;
        }

        /* @var $shipmentModel Mage_Sales_Model_Order_Shipment */
        $shipmentModel = $this->_getCreatedShipmentModel($orderModel);

        /* @var $trackingCreatorService Powerbody_Bridge_Model_Service_Order_Shipment_Tracking_Creator */
        $trackingCreatorService = Mage::getSingleton('bridge/service_order_shipment_tracking_creator');
        $trackingCreatorService->assignTrackNumber($shipmentModel, $trackNumber);
    }

    /**
     * @param Mage_Sales_Model_Order $orderModel
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    private function _getCreatedShipmentModel(Mage_Sales_Model_Order $orderModel)
    {
        /* @var $shipmentCreatorService Powerbody_Bridge_Model_Service_Order_Shipment_Creator */
        $shipmentCreatorService = Mage::getSingleton('bridge/service_order_shipment_creator');

        return $shipmentCreatorService->createShipment($orderModel);
    }
}
