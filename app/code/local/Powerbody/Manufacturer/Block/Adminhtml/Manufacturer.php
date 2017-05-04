<?php

/**
 * Class Powerbody_Manufacturer_Block_Adminhtml_Manufacturer
 */
class Powerbody_Manufacturer_Block_Adminhtml_Manufacturer extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    /**
     * constructor
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_manufacturer';
        $this->_blockGroup = 'manufacturer';
        $this->_headerText = $this->__('Manufacturers');
        parent::__construct();
        $this->_removeButton('add');
    }

}
