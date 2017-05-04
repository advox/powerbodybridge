<?php

/**
 * Class Powerbody_Bridge_Helper_Import
 */
class Powerbody_Bridge_Helper_Import extends Mage_Core_Helper_Abstract
{
    /**
     * @param array $importedCategory
     *
     * @return mixed
     */
    public function renderImportedCategoryRow($importedCategory)
    {
        return Mage::app()->getLayout()->createBlock('bridge/adminhtml_import_renderer_row')
            ->setData('imported_category', $importedCategory)
            ->toHtml();
    }
}
