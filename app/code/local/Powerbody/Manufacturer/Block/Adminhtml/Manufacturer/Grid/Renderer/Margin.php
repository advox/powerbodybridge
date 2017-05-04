<?php

/**
 * Class Powerbody_Manufacturer_Block_Adminhtml_Manufacturer_Grid_Renderer_Margin
 */
class Powerbody_Manufacturer_Block_Adminhtml_Manufacturer_Grid_Renderer_Margin
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('manufacturer/grid/renderer/margin.phtml');
    }

    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(\Varien_Object $row)
    {
        $this->setData('row', $row);
        return $this->_toHtml();
    }

    /**
     * @return bool
     */
    protected function _isCurrentMarginLowerThanMinimal()
    {
        return $this->_getMinimalMargin() > $this->getData('row')->getData('margin');
    }

    /**
     * @return mixed
     */
    protected function _getMinimalMargin()
    {
        return Mage::helper('manufacturer')->getMinimalMargin();
    }
}
