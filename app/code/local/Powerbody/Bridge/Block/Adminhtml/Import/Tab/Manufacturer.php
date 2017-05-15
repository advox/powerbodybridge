<?php

/**
 * Class Powerbody_Bridge_Block_Adminhtml_Import_Tab_Manufacturer
 */
class Powerbody_Bridge_Block_Adminhtml_Import_Tab_Manufacturer extends Mage_Adminhtml_Block_Template
{
    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('bridge/import/manufacturer.phtml');
    }

    /**
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('*/*/manufacturerSave');
    }

    /**
     * @return Powerbody_Bridge_Model_Mysql4_Imported_Manufacturer_Collection
     */
    public function getImportedManufacturers()
    {
        /* @var $importedManufacturersCollection Powerbody_Bridge_Model_Mysql4_Imported_Manufacturer_Collection */
        $importedManufacturersCollection = Mage::getModel('bridge/imported_manufacturer')
            ->getCollection()
            ->addFieldToFilter('dropshipping_status', Powerbody_Bridge_Model_Imported_Manufacturer::DROPSHIPPING_STATUS_ENABLED)
            ->setOrder('name', Varien_Data_Collection::SORT_ORDER_ASC);

        return $importedManufacturersCollection;
    }
}
