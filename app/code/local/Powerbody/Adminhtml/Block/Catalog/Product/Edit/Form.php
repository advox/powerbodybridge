<?php

class Powerbody_Adminhtml_Block_Catalog_Product_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form([
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/saveLimited'),
            'method' => 'post',
        ]);

        $form->setData('use_container', true);
        $this->setForm($form);

        $fieldset = $form->addFieldset('product_edit_limited_form', [
            'legend' => $this->__('Product Settings'),
            'class' => 'fieldset-wide'
        ]);

        $fieldset->addField('entity_id', 'hidden', [
            'required' => true,
            'name' => 'entity_id'
        ]);

        $fieldset->addField('name', 'text', [
            'label' => $this->__('Name'),
            'required' => false,
            'disabled' => true,
        ]);

        $fieldset->addField('sku', 'text', [
            'label' => $this->__('SKU'),
            'required' => false,
            'disabled' => true,
        ]);

        $fieldset->addField('description', 'textarea', [
            'name' => 'description',
            'label' => $this->__('Description'),
        ]);

        $fieldset->addField('short_description', 'textarea', [
            'name' => 'short_description',
            'label' => $this->__('Short description'),
        ]);

        $fieldset->addField('news_from_date', 'date', [
            'label' => $this->__('Set Product as New from Date'),
            'required' => false,
            'name' => 'news_from_date',
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
        ]);

        $fieldset->addField('news_to_date', 'date', [
            'label' => $this->__('Set Product as New to Date'),
            'required' => false,
            'name' => 'news_to_date',
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
        ]);

        $fieldset->addField('special_price', 'text', [
            'type' => 'decimal',
            'label' => $this->__('Special Price'),
            'input' => 'price',
            'name' => 'special_price',
            'backend' => 'catalog/product_attribute_backend_price',
            'required' => false,
        ]);

        $fieldset->addField('special_from_date', 'date', [
            'label' => $this->__('Special Price From Date'),
            'required' => false,
            'name' => 'special_from_date',
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
        ]);

        $fieldset->addField('special_to_date', 'date', [
            'label' => $this->__('Special Price To Date'),
            'required' => false,
            'name' => 'special_to_date',
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
        ]);

        $fieldset->addField('is_updated_while_import', 'select', [
            'label' => $this->__('Is updated while import'),
            'name' => 'is_updated_while_import',
            'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
        ]);

        $form->setValues(
            Mage::getModel('catalog/product')->load($this->getRequest()->get('id'))->getData()
        );

        return parent::_prepareForm();
    }
}
