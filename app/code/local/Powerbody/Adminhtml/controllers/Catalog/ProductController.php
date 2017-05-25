<?php

class Powerbody_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Controller_Action
{
    public function editLimitedAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * @return Mage_Adminhtml_Controller_Action
     */
    public function saveLimitedAction()
    {
        if (false === $this->_validateFormKey()) {
            $this->_getSession()->addError($this->__('The form key is invalid, please refresh a page and try again.'));
            return $this->_redirectReferer();
        }

        $requestParamsArray = $this->getRequest()->getParams();
        try {
            $product = Mage::getModel('catalog/product')->load($requestParamsArray['entity_id']);
            $product->addData($requestParamsArray);
            $product->save();
            $this->_getSession()->addSuccess($this->__('The product has been saved.'));
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
        }

        return $this->_redirectReferer();
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return (bool)Mage::getSingleton('admin/session')->isAllowed('admin/catalog/products_limited');
    }
}
