<?php

/* @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer->startSetup();

/* @var Mage_Catalog_Model_Resource_Eav_Attribute $attributeCheck */
$attributeCheck = Mage::getResourceModel('catalog/eav_attribute')
    ->loadByCode(Mage_Catalog_Model_Product::ENTITY ,'color');
if (null === $attributeCheck->getId()) {
    $data = array(
        'group'                         => 'General',
        'type'                          => 'int',
        'backend'                       => '',
        'frontend'                      => '',
        'label'                         => 'Color',
        'input'                         => 'select',
        'source'                        => 'eav/entity_attribute_source_table',
        'global'                        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible'                       => true,
        'required'                      => false,
        'user_defined'                  => true,
        'default'                       => '',
        'searchable'                    => false,
        'filterable'                    => false,
        'comparable'                    => false,
        'visible_on_front'              => true,
        'visible_in_advanced_search'    => true,
        'used_in_product_listing'       => true,
        'unique'                        => false,
        'is_html_allowed_on_front'      => true,
        'is_visible_on_front'           => false,
        'used_for_sort_by'              => false,
    );
    $installer->addAttribute(
        Mage_Catalog_Model_Product::ENTITY,
        'color',
        $data
    );
} else {
    $installer->updateAttribute(
        Mage_Catalog_Model_Product::ENTITY,
        $attributeCheck->getId(),
        'source_model',
        'eav/entity_attribute_source_table'
    );
    $installer->updateAttribute(
        Mage_Catalog_Model_Product::ENTITY,
        $attributeCheck->getId(),
        'backend_type',
        'int'
    );
    $installer->updateAttribute(
        Mage_Catalog_Model_Product::ENTITY,
        $attributeCheck->getId(),
        'frontend_input',
        'select'
    );
}
$installer->addAttributeToSet('catalog_product', 'Default', 'General', 'color', 1);

/* @var Mage_Catalog_Model_Resource_Eav_Attribute $attributeCheck */
$attributeCheck = Mage::getResourceModel('catalog/eav_attribute')
    ->loadByCode(Mage_Catalog_Model_Product::ENTITY ,'size');
if (null === $attributeCheck->getId()) {
    $data = array(
        'group'                         => 'General',
        'type'                          => 'int',
        'backend'                       => '',
        'frontend'                      => '',
        'label'                         => 'Size',
        'input'                         => 'select',
        'source'                        => 'eav/entity_attribute_source_table',
        'global'                        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible'                       => true,
        'required'                      => false,
        'user_defined'                  => true,
        'default'                       => '',
        'searchable'                    => false,
        'filterable'                    => false,
        'comparable'                    => false,
        'visible_on_front'              => true,
        'visible_in_advanced_search'    => true,
        'used_in_product_listing'       => true,
        'unique'                        => false,
        'is_html_allowed_on_front'      => true,
        'is_visible_on_front'           => false,
        'used_for_sort_by'              => false,
    );
    $installer->addAttribute(
        Mage_Catalog_Model_Product::ENTITY,
        'size',
        $data
    );
} else {
    $installer->updateAttribute(
        Mage_Catalog_Model_Product::ENTITY,
        $attributeCheck->getId(),
        'source_model',
        'eav/entity_attribute_source_table'
    );
    $installer->updateAttribute(
        Mage_Catalog_Model_Product::ENTITY,
        $attributeCheck->getId(),
        'backend_type',
        'int'
    );
    $installer->updateAttribute(
        Mage_Catalog_Model_Product::ENTITY,
        $attributeCheck->getId(),
        'frontend_input',
        'select'
    );
}
$installer->addAttributeToSet('catalog_product', 'Default', 'General', 'size', 2);

/* @var Mage_Catalog_Model_Resource_Eav_Attribute $attributeCheck */
$attributeCheck = Mage::getResourceModel('catalog/eav_attribute')
    ->loadByCode(Mage_Catalog_Model_Product::ENTITY ,'weight_configurable');
if (null === $attributeCheck->getId()) {
    $data = array(
        'group'                         => 'General',
        'type'                          => 'int',
        'backend'                       => '',
        'frontend'                      => '',
        'label'                         => 'Weight',
        'input'                         => 'select',
        'source'                        => 'eav/entity_attribute_source_table',
        'global'                        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible'                       => true,
        'required'                      => false,
        'user_defined'                  => true,
        'default'                       => '',
        'searchable'                    => false,
        'filterable'                    => false,
        'comparable'                    => false,
        'visible_on_front'              => true,
        'visible_in_advanced_search'    => true,
        'used_in_product_listing'       => true,
        'unique'                        => false,
        'is_html_allowed_on_front'      => true,
        'is_visible_on_front'           => false,
        'used_for_sort_by'              => false,
    );
    $installer->addAttribute(
        Mage_Catalog_Model_Product::ENTITY,
        'weight_configurable',
        $data
    );
} else {
    $installer->updateAttribute(
        Mage_Catalog_Model_Product::ENTITY,
        $attributeCheck->getId(),
        'source_model',
        'eav/entity_attribute_source_table'
    );
    $installer->updateAttribute(
        Mage_Catalog_Model_Product::ENTITY,
        $attributeCheck->getId(),
        'backend_type',
        'int'
    );
    $installer->updateAttribute(
        Mage_Catalog_Model_Product::ENTITY,
        $attributeCheck->getId(),
        'frontend_input',
        'select'
    );
}
$installer->addAttributeToSet('catalog_product', 'Default', 'General', 'weight_configurable', 3);

/* @var Mage_Catalog_Model_Resource_Eav_Attribute $attributeCheck */
$attributeCheck = Mage::getResourceModel('catalog/eav_attribute')
    ->loadByCode(Mage_Catalog_Model_Product::ENTITY , 'flavour');
if (null === $attributeCheck->getId()) {
    $data = array(
        'group'                         => 'General',
        'type'                          => 'int',
        'backend'                       => '',
        'frontend'                      => '',
        'label'                         => 'Flavour',
        'input'                         => 'select',
        'source'                        => 'eav/entity_attribute_source_table',
        'global'                        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible'                       => true,
        'required'                      => false,
        'user_defined'                  => true,
        'default'                       => '',
        'searchable'                    => false,
        'filterable'                    => false,
        'comparable'                    => false,
        'visible_on_front'              => true,
        'visible_in_advanced_search'    => true,
        'used_in_product_listing'       => true,
        'unique'                        => false,
        'is_html_allowed_on_front'      => true,
        'is_visible_on_front'           => false,
        'used_for_sort_by'              => false,
    );
    $installer->addAttribute(
        Mage_Catalog_Model_Product::ENTITY,
        'flavour',
        $data
    );
} else {
    $installer->updateAttribute(
        Mage_Catalog_Model_Product::ENTITY,
        $attributeCheck->getId(),
        'source_model',
        'eav/entity_attribute_source_table'
    );
    $installer->updateAttribute(
        Mage_Catalog_Model_Product::ENTITY,
        $attributeCheck->getId(),
        'backend_type',
        'int'
    );
    $installer->updateAttribute(
        Mage_Catalog_Model_Product::ENTITY,
        $attributeCheck->getId(),
        'frontend_input',
        'select'
    );
}
$installer->addAttributeToSet('catalog_product', 'Default', 'General', 'flavour', 4);

$installer->endSetup();
