<?php
/* @var Mage_Catalog_Model_Resource_Setup $eavInstaller */
$eavInstaller = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$eavInstaller->startSetup();

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

Mage::register('isSecureArea', true);

/* @var $categoryCollection Mage_Catalog_Model_Resource_Category_Collection */
$categoryCollection = Mage::getModel('catalog/category')->getCollection()
    ->addFieldToFilter('entity_id', ['gt' => Mage::app()->getStore()->getRootCategoryId()]);
$categoryCollection->walk('delete');

/* @var $manufacturerCollection Powerbody_Manufacturer_Model_Mysql4_Manufacturer_Collection */
$manufacturerCollection = Mage::getModel('manufacturer/manufacturer')->getCollection();
$manufacturerCollection->walk('delete');

$attr = Mage::getResourceModel('catalog/eav_attribute')
    ->loadByCode('catalog_product', 'is_imported');

if ($attr->getId() === null) {
    $eavInstaller->addAttribute('catalog_product', 'is_imported', [
        'group'             => 'General',
        'type'              => 'int',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Is Imported',
        'input'             => 'text',
        'source'            => 'eav/entity_attribute_source_boolean',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible'           => false,
        'required'          => false,
        'user_defined'      => false,
        'default'           => '0',
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'  => false,
        'unique'            => false,
        'is_configurable'   => false
    ]);
}

$eavInstaller->endSetup();

$installer->endSetup();
