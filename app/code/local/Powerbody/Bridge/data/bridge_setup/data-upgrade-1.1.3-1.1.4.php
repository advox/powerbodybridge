<?php

/* @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer->startSetup();

/* @var Mage_Catalog_Model_Resource_Eav_Attribute $attributeCheck */
$attributeCheck = Mage::getResourceModel('catalog/eav_attribute')
    ->loadByCode(Mage_Catalog_Model_Product::ENTITY ,'is_updated_while_import');

if (null === $attributeCheck->getId()) {
    $installer->addAttribute(
        Mage_Catalog_Model_Product::ENTITY,
        'is_updated_while_import',
        [
            'group'                         => 'General',
            'type'                          => 'int',
            'label'                         => 'Is Updated While Import',
            'input'                         => 'boolean',
            'global'                        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
            'visible'                       => true,
            'required'                      => true,
            'user_defined'                  => true,
            'default'                       => '1',
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
    $installer->addAttributeToSet(Mage_Catalog_Model_Product::ENTITY, 'Default', 'General', 'is_updated_while_import');
}


$productCollection = Mage::getModel('catalog/product');
foreach ($productCollection as $productModel) {
    $productModel->setData('is_updated_while_import', 1);
    $productModel->save();
}

$installer->endSetup();
