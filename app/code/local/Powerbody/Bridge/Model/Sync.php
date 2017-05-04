<?php

/**
 * Class Powerbody_Bridge_Model_Sync
 */
class Powerbody_Bridge_Model_Sync
{
    /* @var Powerbody_Bridge_Model_Service|null */
    protected $_service = null;

    /**
     * construct
     */
    public function __construct()
    {
        $this->_service = Mage::getSingleton('bridge/service');
    }

    /**
     * Update categories and manufacturers for bridge imported module
     */
    public function processImported()
    {
        $this->_getService()->connect();
        $this->_updateImportedCategories();
        $this->_updateImportedManufacturers();
        $this->_getService()->disconnect();
    }

    /**
     * Update categories for bridge imported module
     */
    protected function _updateImportedCategories()
    {
        /* @var $syncModel Powerbody_Bridge_Model_Sync_Imported_Category */
        $syncModel = Mage::getModel('bridge/sync_imported_category');
        $syncModel->processImport();
    }

    /**
     * Update manufacturers for bridge imported module
     */
    protected function _updateImportedManufacturers()
    {
        /* @var $syncModel Powerbody_Bridge_Model_Sync_Imported_Manufacturer */
        $syncModel = Mage::getModel('bridge/sync_imported_manufacturer');
        $syncModel->processImport();
    }

    /**
     * Returns service instance
     *
     * @return Powerbody_Bridge_Model_Service
     */
    protected function _getService()
    {
        return $this->_service;
    }
}
