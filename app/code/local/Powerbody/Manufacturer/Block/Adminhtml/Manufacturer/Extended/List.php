<?php

/**
 * Class Powerbody_Manufacturer_Block_Adminhtml_Manufacturer_Extended_List
 */
class Powerbody_Manufacturer_Block_Adminhtml_Manufacturer_Extended_List extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    /**
     * constructor
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_manufacturer_extended_list';
        $this->_blockGroup = 'manufacturer';
        $this->_headerText = $this->__('Manufacturers');
        parent::__construct();
        $this->_removeButton('add');
    }
    
    /**
     * Get header text
     * @return string
     */
    public function getHeaderText()
    {
        $currentManufacturer = Mage::registry('manufacturer');
        if (true === ($currentManufacturer instanceof Powerbody_Manufacturer_Model_Manufacturer)
            && null !== $currentManufacturer->getId()
        ) {
            return $currentManufacturer->getData('name');
        }
        return $this->_headerText;
    }

}
