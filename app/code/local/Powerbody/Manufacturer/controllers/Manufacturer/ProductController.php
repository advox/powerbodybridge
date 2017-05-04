<?php

/**
 * Class Powerbody_Manufacturer_Manufacturer_ProductController
 */
class Powerbody_Manufacturer_Manufacturer_ProductController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Get grid content.
     */
    public function gridAction()
    {
        $this->_initManufacturer();
        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('manufacturer/adminhtml_manufacturer_extended_list', 'manufacturer.products.list')
                ->toHtml()
        );
    }
    
    /**
     * Display manufacturer products action.
     */
    public function indexAction()
    {
        $this->_initManufacturer();
        $this->_initLayout();
        $this->renderLayout();
    }

    protected function _initLayout()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->_title($this->__('Manufacturers'))->_title($this->__('Manufacturer products'));
    }

    /**
     * Init current manufacturer.
     */
    protected function _initManufacturer()
    {
        $manufacturerModel = Mage::getModel('manufacturer/manufacturer')
            ->load($this->getRequest()->getParam('id'));
        /* @var $manufacturerModel Powerbody_Manufacturer_Model_Manufacturer */
        Mage::register('manufacturer', $manufacturerModel);
    }
    
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/manage_manufactuers/products');
    }

}
