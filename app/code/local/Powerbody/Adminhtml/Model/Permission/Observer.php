<?php

class Powerbody_Adminhtml_Model_Permission_Observer
{
    /**
     * @param Varien_Event_Observer $observer
     */
    public function redirectToLimitedIfNoPermission(Varien_Event_Observer $observer)
    {
        /* @var $adminSession Mage_Admin_Model_Session*/
        $adminSession = Mage::getSingleton('admin/session');
        if ($adminSession->isAllowed('admin/catalog/products_full')) {
            return;
        }

        /* @var $controller Mage_Adminhtml_Catalog_ProductController */
        $controller = $observer->getEvent()->getData('controller_action');

        /* @var $adminhtmlHelper Mage_Adminhtml_Helper_Data */
        $adminhtmlHelper = Mage::helper('adminhtml');
        $controller->getResponse()->setRedirect(
            $adminhtmlHelper->getUrl('adminhtml/catalog_product/editLimited', ['id' => $controller->getRequest()->get('id')])
        );
        return;
    }
}
