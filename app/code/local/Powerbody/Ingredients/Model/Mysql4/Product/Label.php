<?php

/**
 * Class Powerbody_Ingredients_Model_Mysql4_Product_Label
 */
class Powerbody_Ingredients_Model_Mysql4_Product_Label extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('ingredients/product_label', 'id');
    }
}
