<?php

/**
 * Class Powerbody_Bridge_Block_Adminhtml_Import_Tabs
 */
class Powerbody_Bridge_Block_Adminhtml_Import_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('product_import_tabs');
        $this->setDestElementId('content');
        $this->setTitle($this->__('Bridge import'));
    }

    /**
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'categories',
            [
                'label'     => $this->__('Categories'),
                'content'   => $this->_getHtmlForBlock('bridge_import_tab_category'),
            ]
        );
        $this->addTab(
            'manufacturers',
            [
                'label'     => $this->__('Manufacturers'),
                'content'   => $this->_getHtmlForBlock('bridge_import_tab_manufacturer'),
            ]
        );
        $this->_updateActiveTab();

        return parent::_beforeToHtml();
    }

    /**
     * Update Tab Active
     */
    protected function _updateActiveTab()
    {
        $tabId = $this->getRequest()->getParam('tab');
        if (null !== $tabId) {
            $tabId = preg_replace("#{$this->getId()}_#", '', $tabId);

            if (false === empty($tabId)) {
                $this->setActiveTab($tabId);
            }
        }
    }

    /**
     * @param string $type
     *
     * @return string
     */
    private function _getHtmlForBlock($type)
    {
        return $this->getLayout()->getBlock($type)->toHtml();
    }
}
