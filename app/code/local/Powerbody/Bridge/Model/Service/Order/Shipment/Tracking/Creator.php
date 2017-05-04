<?php

/**
 * Class Powerbody_Bridge_Model_Service_Order_Shipment_Tracking_Creator
 */
class Powerbody_Bridge_Model_Service_Order_Shipment_Tracking_Creator
{
    /**
     * @param Mage_Sales_Model_Order_Shipment $shipmentModel
     * @param string                          $trackNumber
     */
    public function assignTrackNumber(
        Mage_Sales_Model_Order_Shipment $shipmentModel,
        $trackNumber
    )
    {
        /* @var $orderModel Mage_Sales_Model_Order */
        $orderModel = $shipmentModel->getOrder();

        /* @var $carrierModel Mage_Shipping_Model_Carrier_Abstract */
        $carrierModel = $orderModel->getShippingCarrier();

        if (true === ($carrierModel instanceof Mage_Shipping_Model_Carrier_Abstract)
            && false === $carrierModel->isTrackingAvailable()
        ) {
            return;
        }

        $shipmentIncrementId = $shipmentModel->getData('increment_id');
        $carrierCode = $carrierModel->getCarrierCode();

        /* @var $shipmentApiModel Mage_Sales_Model_Order_Shipment_Api */
        $shipmentApiModel = Mage::getModel('sales/order_shipment_api');
        $shipmentApiModel->addTrack($shipmentIncrementId, $carrierCode, $carrierCode, $trackNumber);
    }
}
