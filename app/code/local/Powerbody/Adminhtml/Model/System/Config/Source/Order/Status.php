<?php

/**
 * Class Powerbody_Adminhtml_Model_System_Config_Source_Order_Status
 */
class Powerbody_Adminhtml_Model_System_Config_Source_Order_Status
{
    const ORDER_AVAILABLE_STATUSES_TO_POWERBODY_CREATE_ORDER_CONFIG_PATH =
        'bridge_settings/dropshipping/available_order_statuses_to_powerbody_create_order';
    const ORDER_ACTIVE_STATUSES_TO_POWERBODY_UPDATE_ORDER_CONFIG_PATH =
        'bridge_settings/dropshipping/active_order_statuses_to_powerbody_update_order';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        /* @var $orderStatusCollection Mage_Sales_Model_Mysql4_Order_Status_Collection */
        $orderStatusCollection = Mage::getModel('sales/order_status')->getCollection();

        $options = [];

        /* @var $orderStatusModel Mage_Sales_Model_Order_Status */
        foreach ($orderStatusCollection as $orderStatusModel) {
            $options[] = [
                'value' => $orderStatusModel->getData('status'),
                'label' => $orderStatusModel->getData('label'),
            ];
        }

        return $options;
    }

    /**
     * @return array
     */
    public function getOrderAvailableStatusesToPowerbodyCreateOrder()
    {
        return $this->_getStatusesArrayByConfigPath(
            self::ORDER_AVAILABLE_STATUSES_TO_POWERBODY_CREATE_ORDER_CONFIG_PATH
        );
    }

    /**
     * @return array
     */
    public function getOrderActiveStatusesToPowerbodyUpdateOrder()
    {
        return $this->_getStatusesArrayByConfigPath(
            self::ORDER_ACTIVE_STATUSES_TO_POWERBODY_UPDATE_ORDER_CONFIG_PATH
        );
    }

    /**
     * @param string $storeConfigPath
     *
     * @return array
     */
    private function _getStatusesArrayByConfigPath($storeConfigPath)
    {
        $statuses = Mage::getStoreConfig($storeConfigPath);

        return $this->_getStatusesArray($statuses);
    }

    /**
     * @param string $statuses
     *
     * @return array
     */
    private function _getStatusesArray($statuses)
    {
        if (true === empty($statuses)) {
            return [];
        }

        return explode(',', $statuses);
    }
}
