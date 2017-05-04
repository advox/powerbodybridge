<?php

/* @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer->startSetup();

/* @var Mage_Catalog_Model_Resource_Eav_Attribute $attributeCheck */
$attributeCheck = Mage::getResourceModel('catalog/eav_attribute')
    ->loadByCode(Mage_Catalog_Model_Product::ENTITY ,'ean');

if (null === $attributeCheck->getId()) {
    $installer->addAttribute(
        Mage_Catalog_Model_Product::ENTITY,
        'ean',
        [
            'group'                         => 'General',
            'type'                          => 'varchar',
            'label'                         => 'Ean',
            'input'                         => 'text',
            'global'                        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
            'visible'                       => true,
            'required'                      => false,
            'user_defined'                  => true,
            'default'                       => '',
            'searchable'                    => false,
            'filterable'                    => false,
            'comparable'                    => false,
            'visible_on_front'              => false,
            'visible_in_advanced_search'    => false,
            'used_in_product_listing'       => false,
            'unique'                        => false,
            'is_html_allowed_on_front'      => false,
            'is_visible_on_front'           => false,
            'used_for_sort_by'              => false,
            'apply_to'                      => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE
        ]
    );
    $installer->addAttributeToSet(Mage_Catalog_Model_Product::ENTITY, 'Default', 'General', 'ean');
}

$installer->endSetup();
