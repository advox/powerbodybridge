<?php

/**
 * Class Powerbody_Manufacturer_Block_Adminhtml_Manufacturer_Extended_List_Grid
 */
class Powerbody_Manufacturer_Block_Adminhtml_Manufacturer_Extended_List_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->setUseAjax(true);
        $this->setId('manufacturerViewGrid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array(
            '_current' => true
        ));
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $productsCollection = Mage::getSingleton('manufacturer/provider_product')
            ->getProductCollection(Mage::registry('manufacturer')->getId());
        /* @var $productsCollection Mage_Catalog_Model_Resource_Product_Collection */
        $productsCollection
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku');

        $this->setCollection($productsCollection);
        return parent::_prepareCollection();
    }
    
    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => $this->__('Id'),
            'index'     => 'entity_id',
            'sortable'  => true,
            'width'     => '60px',
        ));
        $this->addColumn('name', array(
            'header'    => $this->__('Name'),
            'index'     => 'name',
        ));
        $this->addColumn('sku', array(
            'header'    => $this->__('SKU'),
            'index'     => 'sku',
            'width'     => '60px',
        ));
        
        return parent::_prepareColumns();
    }

}
