<?php

/**
 * Class Powerbody_Adminhtml_Model_Observer
 */
class Powerbody_Adminhtml_Model_Observer
{
    /**
     * @param Varien_Event_Observer $observer
     */
    public function onBlockHtmlBefore(Varien_Event_Observer $observer)
    {
        /* @var $block Mage_Adminhtml_Block_Page */
        $block = $observer->getBlock();

        if (true === $block instanceof Mage_Adminhtml_Block_Catalog_Product_Grid) {
            $block->addColumnAfter('is_updated_while_import',
                [
                    'filter'    =>  false,
                    'header'    =>  Mage::helper('bridge')->__('Import'),
                    'index'     =>  'is_updated_while_import',
                    'renderer'  =>  'powerbody_adminhtml/catalog_product_grid_renderer_isupdatedwhileimport',
                    'width'     =>  '50px',
                ],
                'status'
            );
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function onEavLoadBefore(Varien_Event_Observer $observer)
    {
        /* @var $catalogProductCollection Mage_Catalog_Model_Resource_Product_Collection */
        $catalogProductCollection = $observer->getCollection();

        if (true === $catalogProductCollection instanceof Mage_Catalog_Model_Resource_Product_Collection) {
            $catalogProductCollection->addAttributeToSelect('is_updated_while_import');
        }
    }
}
