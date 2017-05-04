<?php

/**
 * Class Powerbody_Manufacturer_Block_Adminhtml_Manufacturer_Grid
 */
class Powerbody_Manufacturer_Block_Adminhtml_Manufacturer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct() 
    {
        parent::__construct();
        $this->setUseAjax(true);
        $this->setId('manufacturerGrid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
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
     * @param Powerbody_Manufacturer_Model_Manufacturer $row
     * @return bool
     */
    public function getRowUrl($row) 
    {
        return false;
    }
    
    /**
     * @return $this
     */
    protected function _prepareCollection() 
    {
        $this->setCollection(
            Mage::getSingleton('manufacturer/provider_manufacturer')->getCollection()
        );
        
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns() 
    {
        $this->addColumn('name', array(
            'header'    => $this->__('Name'),
            'align'     => 'left',
            'index'     => 'name'
        ));

        $this->addColumn('margin', array(
            'header'    => $this->__('Price margin'),
            'align'     => 'left',
            'index'     => 'margin',
            'width'     => '68px',
            'renderer'  => 'manufacturer/adminhtml_manufacturer_grid_renderer_margin'
        ));

        $this->addColumn('priority', array(
            'header'    => $this->__('Priority'),
            'align'     => 'left',
            'width'     => '50px',
            'index'     => 'priority'
        ));

        $this->addColumn('created_date', array(
            'format'    => 'dd/MM/Y HH:mm',
            'header'    => $this->__('Created date'),
            'index'     => 'created_date',
            'type'      => 'datetime',
            'width'     => '80px',
        ));

        $this->addColumn('updated_date', array(
            'format'    => 'dd/MM/Y HH:mm',
            'header'    => $this->__('Updated date'),
            'index'     => 'updated_date',
            'type'      => 'datetime',
            'width'     => '80px',
        ));
        
        return parent::_prepareColumns();
    }
    
}
