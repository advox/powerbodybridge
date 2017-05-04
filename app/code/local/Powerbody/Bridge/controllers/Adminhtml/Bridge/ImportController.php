<?php

/**
 * Class Powerbody_Bridge_Adminhtml_ImportController
 */
class Powerbody_Bridge_Adminhtml_Bridge_ImportController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_initAction();
        /* @var $syncModel Powerbody_Bridge_Model_Sync */
        $syncModel = Mage::getModel('bridge/sync');
        $syncModel->processImported();
        $this->renderLayout();
    }

    /**
     * Category save action
     */
    public function categorySaveAction()
    {
        $categoriesArray = $this->getRequest()->getPost('category');
        if (null === $categoriesArray) {
            $categoriesArray = [];
        }
        try {
            /* @var $serviceCategory Powerbody_Bridge_Model_Service_Category */
            $serviceCategory = $this->_getCategoryService();
            $serviceCategory->processImportedCategoriesSave($categoriesArray);
            $this->_getAdminhtmlSession()->addSuccess($this->__('Changes has been saved.'));
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getAdminhtmlSession()->addError($this->__('Error while saving changes.'));
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Manufacturer save action
     */
    public function manufacturerSaveAction()
    {
        $manufacturersArray = $this->getRequest()->getPost('manufacturer');
        if (null === $manufacturersArray) {
            $manufacturersArray = [];
        }
        try {
            /* @var $serviceManufacturer Powerbody_Bridge_Model_Service_Manufacturer */
            $serviceManufacturer = $this->_getManufacturerService();
            $serviceManufacturer->processImportedManufacturersSave($manufacturersArray);
            $this->_getAdminhtmlSession()->addSuccess($this->__('Changes has been saved.'));
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getAdminhtmlSession()->addError($this->__('Error while saving changes.'));
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Init action
     */
    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('bridge');
        $this->_title($this->__('Bridge'))->_title($this->__('Import requested'));
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('bridge/sync_request');
    }

    /**
     * @return Powerbody_Bridge_Model_Service_Category
     */
    protected function _getCategoryService()
    {
        return Mage::getSingleton('bridge/service_category');
    }

    /**
     * @return Powerbody_Bridge_Model_Service_Manufacturer
     */
    protected function _getManufacturerService()
    {
        return Mage::getSingleton('bridge/service_manufacturer');
    }

    /**
     * @return Mage_Adminhtml_Model_Session
     */
    private function _getAdminhtmlSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }
}
