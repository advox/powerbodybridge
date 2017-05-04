<?php

/**
 * Class Powerbody_Bridge_Model_Sync_Attribute
 */
class Powerbody_Bridge_Model_Sync_Attribute extends Powerbody_Bridge_Model_Sync_Abstract
{
    const FRONTEND_INPUT = 'select';
    const SOURCE_MODEL = 'eav/entity_attribute_source_table';
    const ENTITY_TYPE_PRODUCT_CATALOG_ID = 4;

    /* @var string */
    protected $_serviceMethod = 'getAttributes';

    /* @var Mage_Core_Model_Resource */
    protected $_resourceModel;

    /* @var Magento_Db_Adapter_Pdo_Mysql */
    protected $_readConnection;

    /* @var Magento_Db_Adapter_Pdo_Mysql */
    protected $_writeConnection;

    /** @var null|array */
    private $_attributesForMapping = null;

    /**
     * Powerbody_Bridge_Model_Sync_Attribute constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->_resourceModel = Mage::getSingleton('core/resource');
        $this->_readConnection = $this->_resourceModel->getConnection('core_read');
        $this->_writeConnection = $this->_resourceModel->getConnection('core_write');
    }

    public function processImport()
    {
        $attributesToProcess = $this->_getAttributesToProcess();
        $attributesOptionsWithCodes = $this->_getAttributesOptionsForCode($attributesToProcess);
        if (!is_array($attributesOptionsWithCodes)) {
            return;
        }

        foreach ($attributesOptionsWithCodes as $attributeCode => $attributesOptionsForCode) {
            $this->_processAttributeOptions($attributeCode, $attributesOptionsForCode);
        }
    }

    /**
     * @param int   $entityTypeId
     * @param array $attributeCodes
     *
     * @return array|null
     */
    public function getAttributesForMapping($entityTypeId, array $attributeCodes)
    {
        $connection = $this->_readConnection;
        $query = $connection->select()
            ->from(['ea' => $this->_resourceModel->getTableName('eav/attribute')])
            ->join(
                ['eao' => $this->_resourceModel->getTableName('eav/attribute_option')],
                'ea.attribute_id = eao.attribute_id',
                []
            )
            ->join(
                ['eaov' => $this->_resourceModel->getTableName('eav/attribute_option_value')],
                'eao.option_id = eaov.option_id',
                ['eaov_option_id' => 'option_id', 'eaov_value' => 'value']
            )
            ->where('ea.attribute_code in (?)', $attributeCodes)
            ->where('ea.entity_type_id = ?', $entityTypeId)
        ;

        foreach ($connection->fetchAll($query) as $attribute) {
            $this->_attributesForMapping[$entityTypeId][$attribute['attribute_code']][$attribute['eaov_value']] =
                $attribute['eaov_option_id'];
        }

        return $this->_attributesForMapping;
    }

    /**
     * @param string $attributeCode
     * @param array  $attributesOptionsForCode
     */
    protected function _processAttributeOptions($attributeCode, $attributesOptionsForCode)
    {
        $connection = $this->_writeConnection;
        try {
            $connection->beginTransaction();
            if (false === empty($attributesOptionsForCode)) {
                /* @var Mage_Eav_Model_Entity_Attribute $attribute */
                $attribute = Mage::getModel('eav/entity_attribute')
                    ->loadByCode(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
                foreach ($attributesOptionsForCode as $attributeOption) {
                    $this->_processSingleAttributeOption($attribute, $attributeOption);
                }
            }
            $connection->commit();
        } catch (Exception $e) {
            $connection->rollBack();
            $this->_getLog()->logException($e);
        }
    }

    /**
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @param string $attributeOption
     */
    protected function _processSingleAttributeOption($attribute, $attributeOption)
    {
        if (true === isset($attributeOption['value'])
            && false === empty($attributeOption['value'])
        ) {
            $attributeOptionId = $attributeOption['value'];
            $attributeOptionValue = $attributeOption['label'];
            $option = $this->_checkIfAttributeOptionExists($attributeOptionValue, $attribute->getId());
            if (false === $option) {
                $this->_saveItem(
                    $attributeOptionId,
                    [
                        'attribute' => $attribute,
                        'value'     => $attributeOptionValue,
                    ]
                );
            }
        }
    }

    /**
     * @param string $attributeOptionValue
     * @param int $attributeId
     *
     * @return string
     */
    protected function _checkIfAttributeOptionExists($attributeOptionValue, $attributeId)
    {
        /* @var Varien_Db_Select $select */
        $select = $this->_readConnection->select()
            ->from(
                ['eaov' => $this->_resourceModel->getTableName('eav_attribute_option_value')]
            )
            ->joinLeft(
                ['eao' => $this->_resourceModel->getTableName('eav_attribute_option')],
                'eaov.option_id = eao.option_id',
                []
            )
            ->where('value = ?', $attributeOptionValue)
            ->where('eao.attribute_id = ?', $attributeId);

        return $this->_readConnection->fetchOne($select);
    }

    /**
     * @param array $attributesToProcess
     *
     * @return array|null
     * @throws Exception
     */
    protected function _getAttributesOptionsForCode($attributesToProcess)
    {
        $serviceParams = ['attribute_code' => $attributesToProcess];

        return $this->_makeServiceMethodRequest($this->_serviceMethod, $serviceParams);
    }
    
    /**
     * @return array
     */
    protected function _getAttributesToProcess()
    {
        /* @var Mage_Eav_Model_Resource_Entity_Attribute_Collection $attibuteCollection */
        $attributeCollection = Mage::getModel('eav/entity_attribute')->getCollection()
            ->addFieldToFilter('entity_type_id', self::ENTITY_TYPE_PRODUCT_CATALOG_ID)
            ->addFieldToFilter('frontend_input', self::FRONTEND_INPUT)
            ->addFieldToFilter('source_model', self::SOURCE_MODEL);

        return $attributeCollection->getColumnValues('attribute_code');
    }

    /**
     * @param int $id
     * @param array $itemData
     */
    protected function _saveItem($id, $itemData)
    {
        if (null === $id || empty($itemData) || !isset($itemData['value'])) {
            return;
        }

        try {
            /* @var Mage_Eav_Model_Entity_Attribute $attribute */
            $attribute = $itemData['attribute'];
            $option['attribute_id'] = $attribute->getId();
            $option['value'][$attribute['attribute_code'] . $id][0] = $itemData['value'];
            /** @var Mage_Eav_Model_Entity_Setup $setup */
            $setup = Mage::getModel('eav/entity_setup', 'core_setup');
            $setup->addAttributeOption($option);
        } catch (Exception $e) {
            $this->_getLog()->logException($e);
        }
    }
}
