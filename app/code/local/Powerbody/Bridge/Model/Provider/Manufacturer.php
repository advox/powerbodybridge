<?php

/**
 * Class Powerbody_Bridge_Model_Provider_Manufacturer
 */
class Powerbody_Bridge_Model_Provider_Manufacturer
{
    /**
     * @var array $manufacturersIds
     *
     * @return Powerbody_Bridge_Model_Mysql4_Imported_Manufacturer_Collection
     */
    public function getImportedManufacturersToSave($manufacturersIds)
    {
        /* @var $bridgeImportedManufacturerCollection Powerbody_Bridge_Model_Mysql4_Imported_Manufacturer_Collection */
        $bridgeImportedManufacturerCollection = Mage::getModel('bridge/imported_manufacturer')->getCollection();

        return $bridgeImportedManufacturerCollection->addFieldToFilter('id', ['in' => $manufacturersIds]);
    }

    /**
     * @return array
     */
    public function getBeforeCleanClientManufacturerIds()
    {
        /* @var $importedManufacturerCollection Powerbody_Bridge_Model_Mysql4_Imported_Manufacturer_Collection */
        $importedManufacturerCollection = Mage::getModel('bridge/imported_manufacturer')->getCollection();
        $importedManufacturerCollection->getSelect()->where('is_selected = ?', 1);

        return $importedManufacturerCollection->getData();
    }

    /**
     * @param array $manufacturersArray
     *
     * @return array
     */
    public function prepareImportedManufacturersForWebService($manufacturersArray)
    {
        /* @var $importedManufacturerCollection Powerbody_Bridge_Model_Mysql4_Imported_Manufacturer_Collection */
        $importedManufacturerCollection = Mage::getModel('bridge/imported_manufacturer')
            ->getCollection()
            ->addFieldToFilter('id', ['in' => $manufacturersArray])
            ->getColumnValues('base_manufacturer_id');
        $importedManufacturerIds = [];
        foreach ($importedManufacturerCollection as $importedManufacturerId) {
            $importedManufacturerIds[$importedManufacturerId]['id'] = $importedManufacturerId;
        }

        return $importedManufacturerIds;
    }

    /**
     * @param array $originalData
     * @param array $postData
     *
     * @return array
     */
    public function getCatalogManufacturersToDelete($originalData, $postData)
    {
        $tmpOriginalData = [];
        foreach ($originalData as $original) {
            $tmpOriginalData[$original['id']] = $original['id'];
        }

        $manufacturersToDelete = array_diff($tmpOriginalData, $postData);
        $catalogManufacturersBaseIds = [];
        foreach ($originalData as $original) {
            if (true === in_array($original['id'], $manufacturersToDelete)) {
                $catalogManufacturersBaseIds[$original['id']]['id'] = $original['id'];
                $catalogManufacturersBaseIds[$original['id']]['base'] = $original['base_manufacturer_id'];
                $catalogManufacturersBaseIds[$original['id']]['client'] = $original['client_manufacturer_id'];
            }
        }

        return $catalogManufacturersBaseIds;
    }
}
