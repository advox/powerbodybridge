<?php

/**
 * Class Powerbody_Ingredients_Model_Mysql4_Product_Label_Image
 */
class Powerbody_Ingredients_Model_Mysql4_Product_Label_Image
    extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('ingredients/product_label_image', 'id');
    }

}
