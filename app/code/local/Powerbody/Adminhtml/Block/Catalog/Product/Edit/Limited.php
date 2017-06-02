<?php

class Powerbody_Adminhtml_Block_Catalog_Product_Edit_Limited extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'powerbody_adminhtml';
        $this->_controller = 'catalog_product';
        $this->_headerText = $this->__('Edit Product');

        $this->_removeButton('delete');
    }
}
