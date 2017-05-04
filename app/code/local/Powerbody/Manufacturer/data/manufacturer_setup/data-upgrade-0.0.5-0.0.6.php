<?php
/** @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

/**
 * ASSIGN MANUFACTURER ATTRIBUTE TO ATTRIBUTE GROUP AND SET
 */
$attributeSetName = 'default';
$groupName = 'general';
$attributeCode = 'manufacturer';

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$attributeSetId = $setup->getAttributeSetId(Mage_Catalog_Model_Product::ENTITY, $attributeSetName);
$attributeGroupId = $setup->getAttributeGroupId(Mage_Catalog_Model_Product::ENTITY, $attributeSetId, $groupName);
$attributeId = $setup->getAttributeId(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
$setup->addAttributeToSet(Mage_Catalog_Model_Product::ENTITY, $attributeSetId, $attributeGroupId, $attributeId);

/**
 * CHANGING MANUFACTURER ATTRIBUTE SOURCE MODEL
 */
$attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
if ($attributeModel instanceof Mage_Eav_Model_Entity_Attribute && $attributeModel->getAttributeCode() === $attributeCode) {
    $attributeModel->setData('source_model', 'manufacturer/source_manufacturer');
    $attributeModel->save();
}
