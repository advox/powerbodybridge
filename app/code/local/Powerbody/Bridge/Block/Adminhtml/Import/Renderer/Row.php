<?php

/**
 * Class Powerbody_Bridge_Block_Adminhtml_Import_Renderer_Row
 */
class Powerbody_Bridge_Block_Adminhtml_Import_Renderer_Row
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Powerbody_Bridge_Block_Adminhtml_Import_Renderer_Row constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('/bridge/import/renderer/row.phtml');
    }

    /**
     * @param Varien_Object $row
     *
     * @return string
     */
    public function render(\Varien_Object $row)
    {
        return $this->_toHtml();
    }
}
