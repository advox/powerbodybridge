<?php

/**
 * Class Powerbody_Ingredients_Model_Label
 */
class Powerbody_Ingredients_Model_Product_Label extends Mage_Core_Model_Abstract
{
    const STATUS_UP_TO_DATE     = 1;
    const STATUS_NEED_UPDATE    = 2;
    const STATUS_TO_DELETE      = 3;

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('ingredients/product_label');
    }
}
