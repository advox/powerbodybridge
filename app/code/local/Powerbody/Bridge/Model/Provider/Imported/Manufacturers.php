<?php

/**
 * Class Powerbody_Bridge_Model_Provider_Imported_Manufacturers
 */
class Powerbody_Bridge_Model_Provider_Imported_Manufacturers
{
    /* @var null|array */
    private $_importedManufacturers = null;

    /**
     * @return Powerbody_Bridge_Model_Mysql4_Imported_Manufacturer_Collection
     */
    public function getImportedManufacturers()
    {
        if (false === is_array($this->_importedManufacturers)) {
            /** @var Powerbody_Bridge_Model_Mysql4_Imported_Manufacturer_Collection $this->_importedManufacturers */
            $importedManufacturersCollection = Mage::getModel('bridge/imported_manufacturer')->getCollection();
            $importedManufacturersCollection->removeAllFieldsFromSelect();
            $importedManufacturersCollection->addFieldToSelect(['is_selected', 'base_manufacturer_id', 'client_manufacturer_id']);
            $this->_importedManufacturers = $importedManufacturersCollection->getData();
        }

        return $this->_importedManufacturers;
    }

    /**
     * @return array
     */
    public function getSelectedImportedManufacturers()
    {
        $selectedImportedManufacturers = [];
        $importedManufacturersArray = $this->getImportedManufacturers();
        foreach ($importedManufacturersArray as $importedManufacturer) {
            if ($importedManufacturer['is_selected'] === '1') {
                $selectedImportedManufacturers[] = $importedManufacturer;
            }
        }

        return $selectedImportedManufacturers;
    }

    /**
     * @return array
     */
    public function getSelectedImportedManufacturersBaseIds()
    {
        $importedManufacturersArray = $this->getSelectedImportedManufacturers();

        return array_column($importedManufacturersArray, 'base_manufacturer_id');

    }

    /**
     * @return array
     */
    public function getSelectedImportedManufacturersInternalIds()
    {
        $importedManufacturersArray = $this->getSelectedImportedManufacturers();

        return array_column($importedManufacturersArray, 'client_manufacturer_id');

    }

    /**
     * @return array
     */
    public function getNotSelectedImportedManufacturers()
    {
        $notSelectedImportedManufacturers = [];
        $importedManufacturersArray = $this->getImportedManufacturers();
        foreach ($importedManufacturersArray as $importedManufacturer) {
            if ($importedManufacturer['is_selected'] === '0') {
                $notSelectedImportedManufacturers[] = $importedManufacturer;
            }
        }

        return $notSelectedImportedManufacturers;
    }

    /**
     * @return array
     */
    public function getNotSelectedImportedManufacturersInternalIds()
    {
        $importedManufacturersArray = $this->getNotSelectedImportedManufacturers();

        return array_column($importedManufacturersArray, 'client_manufacturer_id');
    }
}
