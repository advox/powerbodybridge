<?php

/**
 * Class Powerbody_Bridge_Model_Service_Manufacturer
 */
class Powerbody_Bridge_Model_Service_Manufacturer extends Mage_Core_Model_Abstract
{
    /**
     * @param array $manufacturersArray
     *
     * @throws Exception
     */
    public function processImportedManufacturersSave($manufacturersArray)
    {
        try {
            /* @var $providerManufacturer Powerbody_Bridge_Model_Provider_Manufacturer */
            $providerManufacturer = Mage::getSingleton('bridge/provider_manufacturer');
            /* @var $manufacturerSyncModel Powerbody_Bridge_Model_Sync_Manufacturer */
            $manufacturerSyncModel = Mage::getModel('bridge/sync_manufacturer');

            $beforeClearManufacturerIds = $providerManufacturer->getBeforeCleanClientManufacturerIds();
            $catalogManufacturersToDelete = $providerManufacturer
                ->getCatalogManufacturersToDelete($beforeClearManufacturerIds, $manufacturersArray);
            if (false === empty($catalogManufacturersToDelete)) {
                $this->_deleteCatalogManufacturers($catalogManufacturersToDelete);
            }
            $this->_saveImportedManufacturers($manufacturersArray);
            if (false === empty($manufacturersArray)) {
                $baseManufacturerIds = $providerManufacturer->prepareImportedManufacturersForWebService($manufacturersArray);
                $manufacturerSyncModel->processManufacturersByIds($baseManufacturerIds);
            }
        } catch (Exception $e) {
            throw (new Exception($e->getMessage()));
        }
    }

    /**
     * @param array $importedManufacturers
     */
    protected function _saveImportedManufacturers($importedManufacturers)
    {
        $this->_clearImportedManufacturersSelected();
        /* @var $manufacturerProvider Powerbody_Bridge_Model_Provider_Manufacturer */
        $manufacturerProvider = Mage::getModel('bridge/provider_manufacturer');
        /* @var $importedManufacturers Powerbody_Bridge_Model_Mysql4_Imported_Manufacturer_Collection */
        $importedManufacturers = $manufacturerProvider->getImportedManufacturersToSave($importedManufacturers);
        foreach ($importedManufacturers as $importedManufacturerModel) {
            /* @var $importedManufacturerModel Powerbody_Bridge_Model_Imported_Manufacturer */
            $importedManufacturerModel->setData('is_selected', 1)
                ->save();
        }
    }

    /**
     * @param array $manufacturers
     */
    protected function _deleteCatalogManufacturers($manufacturers)
    {
        foreach ($manufacturers as $manufacturer) {
            $this->_deleteBridgeImportedManufacturer($manufacturer['id']);
            $this->_deleteCatalogManufacturer($manufacturer);
        }
    }

    /**
     * @param array $manufacturer
     */
    protected function _deleteCatalogManufacturer($manufacturer)
    {
        /* @var $catalogCategoryModel Mage_Catalog_Model_Category */
        $catalogManufacturerModel = Mage::getModel('manufacturer/manufacturer')->load($manufacturer['client']);
        $catalogManufacturerModel->delete();
    }

    /**
     * @param int $manufacturerId
     */
    protected function _deleteBridgeImportedManufacturer($manufacturerId)
    {
        /* @var $importedManufacturerModel Powerbody_Bridge_Model_Imported_Category */
        $importedManufacturerModel = Mage::getModel('bridge/imported_manufacturer')->load($manufacturerId);
        $importedManufacturerModel
            ->setData('client_manufacturer_id', new Zend_Db_Expr('null'))
            ->setData('is_selected', 0)
            ->save();
    }

    /**
     * Clear imported categories selected
     */
    protected function _clearImportedManufacturersSelected()
    {
        /* @var $importedManufacturerCollection Powerbody_Bridge_Model_Mysql4_Imported_Manufacturer_Collection */
        $importedManufacturerCollection = Mage::getModel('bridge/imported_manufacturer')->getCollection();
        foreach ($importedManufacturerCollection as $importedManufacturerModel) {
            $importedManufacturerModel
                ->setData('is_selected', 0)
                ->save();
        }
    }
}
