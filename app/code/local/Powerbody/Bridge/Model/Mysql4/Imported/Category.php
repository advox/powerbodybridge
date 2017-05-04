<?php

/**
 * Class Powerbody_Bridge_Model_Mysql4_Imported_Category
 */
class Powerbody_Bridge_Model_Mysql4_Imported_Category extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct() 
    {                
        $this->_init('bridge/imported_category', 'id');
    }
}
