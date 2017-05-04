<?php

/**
 * Class Powerbody_Adminhtml_Block_Catalog_Product_Grid_Renderer_Isupdatedwhileimport
 */
class Powerbody_Adminhtml_Block_Catalog_Product_Grid_Renderer_Isupdatedwhileimport
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * {@inheritdoc}.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('/adminhtml/catalog/product/grid/renderer/is_updated_while_import.phtml');
    }

    /**
     * @param Varien_Object $row
     *
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $this->setRowItem($row);

        return $this->_toHtml();
    }

    /**
     * @param Varien_Object $row
     *
     * @return bool
     */
    public function isProductTypeConfigurable(Varien_Object $row)
    {
        return $row->getData('type_id') === Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE;
    }
}
