<?php

class Powerbody_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Controller_Action
{
    public function editLimitedAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function saveLimitedAction()
    {
        if (false === $this->_validateFormKey()) {
            $this->norouteAction();
        }

        $requestParamsArray = $this->getRequest()->getParams();
        $product = Mage::getModel('catalog/product')->load($requestParamsArray['entity_id']);
        $product->addData($requestParamsArray);
        $product->save();

        $this->_redirectReferer();
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return (bool)Mage::getSingleton('admin/session')->isAllowed('admin/catalog/products_limited');
    }
}