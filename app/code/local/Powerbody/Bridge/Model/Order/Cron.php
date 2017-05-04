<?php

/**
 * Class Powerbody_Bridge_Model_Order_Cron
 */
class Powerbody_Bridge_Model_Order_Cron
{
    /**
     * updateOrders
     */
    public function updateOrders()
    {
        $ordersData = $this->_getOrderData();

        if (true === empty($ordersData) || false === is_array($ordersData)) {
            return;
        }

        $this->_updateOrdersProcess($ordersData);
    }

    /**
     * @return array
     */
    private function _getOrderData()
    {
        /* @var $orderProvider Powerbody_Bridge_Model_Provider_Order */
        $orderProvider = Mage::getSingleton('bridge/provider_order');

        return $orderProvider->getOrdersData();
    }

    /**
     * @param array $ordersData
     */
    private function _updateOrdersProcess(array $ordersData)
    {
        /* @var $orderService Powerbody_Bridge_Model_Service_Order */
        $orderService = Mage::getSingleton('bridge/service_order');

        $orderService->updateOrdersProcess($ordersData);
    }
}
