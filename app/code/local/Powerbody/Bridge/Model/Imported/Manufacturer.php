<?php

/**
 * Class Powerbody_Bridge_Model_Imported_Manufacturer
 */
class Powerbody_Bridge_Model_Imported_Manufacturer extends Mage_Core_Model_Abstract
{
    /**
     * construct
     */
    protected function _construct() 
    {
        parent::_construct();
        $this->_init('bridge/imported_manufacturer');
    }

    /**
     * before save
     */
    protected function _beforeSave()
    {
        $this->setData('updated_date', Mage::getSingleton('core/date')->gmtDate());

        return parent::_beforeSave();
    }
}
