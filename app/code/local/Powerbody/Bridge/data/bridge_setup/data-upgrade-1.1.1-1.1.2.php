<?php

/* @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer->startSetup();

/* @var Mage_Catalog_Model_Resource_Eav_Attribute $attributeCheck */
$attributeCheck = Mage::getResourceModel('catalog/eav_attribute')
    ->loadByCode(Mage_Catalog_Model_Product::ENTITY ,'color');
if (null != $attributeCheck->getId()) {
    $installer->updateAttribute(
        Mage_Catalog_Model_Product::ENTITY,
        $attributeCheck->getId(),
        'default_value',
        null
    );
}

/* @var Mage_Catalog_Model_Resource_Eav_Attribute $attributeCheck */
$attributeCheck = Mage::getResourceModel('catalog/eav_attribute')
    ->loadByCode(Mage_Catalog_Model_Product::ENTITY ,'size');
if (null != $attributeCheck->getId()) {
    $installer->updateAttribute(
        Mage_Catalog_Model_Product::ENTITY,
        $attributeCheck->getId(),
        'default_value',
        null
    );
}

/* @var Mage_Catalog_Model_Resource_Eav_Attribute $attributeCheck */
$attributeCheck = Mage::getResourceModel('catalog/eav_attribute')
    ->loadByCode(Mage_Catalog_Model_Product::ENTITY ,'flavour');
if (null != $attributeCheck->getId()) {
    $installer->updateAttribute(
        Mage_Catalog_Model_Product::ENTITY,
        $attributeCheck->getId(),
        'default_value',
        null
    );
}

/* @var Mage_Catalog_Model_Resource_Eav_Attribute $attributeCheck */
$attributeCheck = Mage::getResourceModel('catalog/eav_attribute')
    ->loadByCode(Mage_Catalog_Model_Product::ENTITY ,'weight_configurable');
if (null != $attributeCheck->getId()) {
    $installer->updateAttribute(
        Mage_Catalog_Model_Product::ENTITY,
        $attributeCheck->getId(),
        'default_value',
        null
    );
}

$installer->endSetup();
