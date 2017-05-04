<?php

/**
 * Class Powerbody_Bridge_Model_Mysql4_Imported_Category_Collection
 */      
class Powerbody_Bridge_Model_Mysql4_Imported_Category_Collection
    extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {       
        parent::_construct();
        $this->_init('bridge/imported_category');
    }
}
