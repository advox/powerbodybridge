<?php

/**
 * Class Powerbody_Bridge_Model_Service_Order_Shipment_Creator
 */
class Powerbody_Bridge_Model_Service_Order_Shipment_Creator
{
    /**
     * @param Mage_Sales_Model_Order $orderModel
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function createShipment(Mage_Sales_Model_Order $orderModel)
    {
        /* @var $shipmentModel Mage_Sales_Model_Order_Shipment */
        $shipmentModel = Mage::getModel('sales/order_shipment')->load($orderModel->getId(), 'order_id');

        if (null !== $shipmentModel->getId()) {
            return $shipmentModel;
        }

        $quantities = $this->_getQuantitiesOfOrderedItems($orderModel);

        if (true === empty($quantities)) {
            return $shipmentModel;
        }

        /* @var $orderService Mage_Sales_Model_Service_Order */
        $orderService = $this->_getOrderService($orderModel);

        /* @var $shipmentModel Mage_Sales_Model_Order_Shipment */
        $shipmentModel = $orderService->prepareShipment($quantities);
        $shipmentModel->register();
        $shipmentModel->save();
        $shipmentModel->afterCommitCallback();

        return $shipmentModel;
    }

    /**
     * @param Mage_Sales_Model_Order $orderModel
     *
     * @return Mage_Sales_Model_Service_Order
     */
    private function _getOrderService(Mage_Sales_Model_Order $orderModel)
    {
        return Mage::getModel('sales/service_order', $orderModel);
    }

    /**
     * @param Mage_Sales_Model_Order $orderModel
     *
     * @return array
     */
    private function _getQuantitiesOfOrderedItems(Mage_Sales_Model_Order $orderModel)
    {
        /* @var $orderItemsCollection Mage_Sales_Model_Resource_Order_Item_Collection */
        $orderItemsCollection = $orderModel->getItemsCollection();

        $quantities = [];

        foreach ($orderItemsCollection as $orderItemModel) {
            $quantities[$orderItemModel->getData('item_id')] =
                $orderItemModel->getData('qty_ordered');
        }

        return $quantities;
    }
}
