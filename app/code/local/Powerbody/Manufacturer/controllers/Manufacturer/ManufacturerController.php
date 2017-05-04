<?php

/**
 * Class Powerbody_Manufacturer_Manufacturer_ManufacturerController
 */
class Powerbody_Manufacturer_Manufacturer_ManufacturerController extends Mage_Adminhtml_Controller_Action
{
    public function gridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('manufacturer/adminhtml_manufacturer_grid')->toHtml()
        );
    }

    public function indexAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }

    public function updateMarginAction()
    {
        $fieldId = (int) $this->getRequest()->getParam('id');
        $margin  = (int) $this->getRequest()->getParam('margin');

        /* @var Powerbody_Manufacturer_Helper_Data $helper */
        $helper = Mage::helper('manufacturer');
        $minimalMargin = $helper->getMinimalMargin();

        if ($margin < $minimalMargin) {
            $this->getResponse()->setHeader('Content-type', 'application/json');
            $this->getResponse()->setBody(json_encode([
                'success' => false,
                'message' => $this->__('Margin cannot be lower than '.$minimalMargin.'.'),
            ]));
            return;
        }
        /* @var Powerbody_Manufacturer_Model_Service_Manufacturer_Margin_Updater $marginUpdater */
        $marginUpdater = Mage::getModel('manufacturer/service_manufacturer_margin_updater');
        $marginUpdater->updateMargin($fieldId, $margin);
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog');
        $this->_title($this->__('Manage Manufacturer'));
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/manage_manufactuers/manufactuers');
    }
}
