<?php

/**
 * Class Powerbody_Ingredients_Model_Mysql4_Product_Label_Collection
 */
class Powerbody_Ingredients_Model_Mysql4_Product_Label_Collection
    extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('ingredients/product_label');
    }
}
