<?php

/**
 * Class Powerbody_Bridge_Model_Provider_Order
 */
class Powerbody_Bridge_Model_Provider_Order
{
    const ORDER_CHUNK_SIZE = 200;

    /**
     * @return array
     */
    public function getOrdersData()
    {
        $orderIds = $this->_getActiveOrdersIds();

        if (true === empty($orderIds)) {
            return [];
        }

        $chunkOrderIds = array_chunk($orderIds, self::ORDER_CHUNK_SIZE);

        $ordersData = [];
        foreach ($chunkOrderIds as $ids) {
            $powerbodyOrders = $this->_getPowerbodyOrders($ids);

            if (true === is_array($powerbodyOrders)) {
                $ordersData = array_merge($ordersData, $powerbodyOrders);
            }
        }

        return $ordersData;
    }

    /**
     * @param int|string $orderId
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrderById($orderId)
    {
        return Mage::getModel('sales/order')->load($orderId, 'entity_id');
    }

    /**
     * @return array
     */
    private function _getActiveOrdersIds()
    {
        $orderActiveStatuses = $this->_getOrderActiveStatusesToPowerbodyUpdateOrder();

        if (true === empty($orderActiveStatuses)) {
            return [];
        }

        /* @var $ordersCollection Mage_Sales_Model_Resource_Order_Collection */
        $ordersCollection = Mage::getModel('sales/order')->getCollection();
        $ordersCollection
            ->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('status')
            ->addAttributeToFilter('status', ['in' => $orderActiveStatuses]);

        return $ordersCollection->getColumnValues('entity_id');
    }

    /**
     * @return array
     */
    private function _getOrderActiveStatusesToPowerbodyUpdateOrder()
    {
        /* @var $configSourceOrderStatusModel Powerbody_Adminhtml_Model_System_Config_Source_Order_Status */
        $configSourceOrderStatusModel = Mage::getModel('powerbody_adminhtml/system_config_source_order_status');

        return $configSourceOrderStatusModel->getOrderActiveStatusesToPowerbodyUpdateOrder();
    }

    /**
     * @param array $ids
     *
     * @return array
     */
    private function _getPowerbodyOrders(array $ids)
    {
        $dropshippingApi = new Dropshipping_Api($this->_getApiConfigData());
        
        $orders = [];
        try {
            $orders = $dropshippingApi->getOrdersForDropclient(['ids' => $ids]);
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $orders;
    }

    /**
     * @return array
     */
    private function _getApiConfigData()
    {
        /* @var $dropshippingHelper Powerbody_Bridge_Helper_Dropshipping */
        $dropshippingHelper = Mage::helper('bridge/dropshipping');

        return $dropshippingHelper->getApiConfigData();
    }
}
