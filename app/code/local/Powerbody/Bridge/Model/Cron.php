<?php

/**
 * Class Powerbody_Bridge_Model_Cron
 */
class Powerbody_Bridge_Model_Cron
{
    /**
     * Create orders in base instance using dropshipping api.
     */
    public function createDropshippingOrders()
    {
        if (Mage::helper('bridge')->getBridgeIsEnabled()) {
            try {
                /* @var $dropshippingOrderModel Powerbody_Bridge_Model_Dropshipping_Order */
                $dropshippingOrderModel = Mage::getModel('bridge/dropshipping_order');
                $dropshippingOrderModel->processCreatingOrders();
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::throwException($e->getMessage());
            }
        }
    }
    
    /**
     * This method update dropshipping orders
     */
    public function updateDropshippingOrders() 
    {
        if (Mage::helper('bridge')->getBridgeIsEnabled()) {
            try {
                /* @var $dropshippingOrderModel Powerbody_Bridge_Model_Dropshipping_Order */
                $dropshippingOrderModel = Mage::getModel('bridge/dropshipping_order');
                $dropshippingOrderModel->updateOrders();
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::throwException($e->getMessage());
            }
        }
    }
}
