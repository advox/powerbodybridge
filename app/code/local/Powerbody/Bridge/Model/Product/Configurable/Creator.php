<?php

/**
 * Class Powerbody_Bridge_Model_Product_Configurable_Creator
 */
class Powerbody_Bridge_Model_Product_Configurable_Creator
{
    const DEFAULT_QTY_IN_STOCK = 0;

    /** @var array */
    private $configurableAttributeCodes = ['size', 'color', 'flavour', 'weight_configurable'];

    /** @var Powerbody_Bridge_Model_Product_Attribute_Loader */
    private $attributeLoader;

    /** @var Mage_Eav_Model_Entity_Attribute[] */
    private $configurableAttributes;

    /**
     * Powerbody_Bridge_Model_Product_Configurable_Creator constructor
     */
    public function __construct()
    {
        $this->attributeLoader = Mage::getModel('bridge/product_attribute_loader');
    }

    /**
     * @param array $product
     * @throws Exception
     */
    public function createConfigurableProduct(array $product)
    {
        $configProduct = $this->_generateConfigurableProduct($product);

        if (false === $configProduct instanceof Mage_Catalog_Model_Product) {
            throw new Exception(sprintf('ERROR creating product: %s', $product['sku']));
        } else {
            $this->_saveProduct($configProduct);
        }
    }

    /**
     * @param array $externalIds
     *
     * @return array
     */
    public function getInternalCategoryIds(array $externalIds)
    {
        /** @var Mage_Catalog_Model_Resource_Category_Collection $categoryCollection */
        $categoryCollection = Mage::getModel('catalog/category')->getCollection();
        $categoryCollection->getSelect()
            ->join(
                $categoryCollection->getResource()->getTable('bridge/imported_category'),
                'client_category_id=entity_id',
                'base_category_id'
            )
            ->where('base_category_id in (?)', $externalIds);

        return $categoryCollection->getColumnValues('entity_id');
    }

    /**
     * @param array $externalIds
     *
     * @return array
     */
    public function getInternalManufacturerIds(array $externalIds)
    {
        /** @var Powerbody_Manufacturer_Model_Mysql4_Manufacturer_Collection $manufacturerCollection */
        $manufacturerCollection = Mage::getModel('manufacturer/manufacturer')->getCollection();
        $manufacturerCollection->getSelect()
            ->join(
                ['bim' => $manufacturerCollection->getResource()->getTable('bridge/imported_manufacturer')],
                'bim.client_manufacturer_id=main_table.id',
                'base_manufacturer_id'
            )
            ->where('base_manufacturer_id in (?)', $externalIds);

        return $manufacturerCollection->getColumnValues('id');
    }

    /**
     * @param array $productData
     *
     * @return bool|Mage_Catalog_Model_Product
     */
    private function _generateConfigurableProduct(array $productData)
    {
        $productData = $this->_setConfigurableProductPrice($productData);
        /* @var $configProduct Mage_Catalog_Model_Product */
        $configProduct = $this->_createConfigurableProduct($productData);
        $configProductTypeInstance = $configProduct->getTypeInstance();
        $simpleProductAttributeCodes = $this->_getSimpleProductConfigurableAttributes($productData);
        if (true === empty($simpleProductAttributeCodes)) {
            return false;
        }
        /** @var $configProductTypeInstance Mage_Catalog_Model_Product_Type_Configurable */
        $this->_setUsedProductAttributeIdsForTypeInstance($configProductTypeInstance, $simpleProductAttributeCodes);
        $childrenArray = $this->_generateChildrenArray($productData, $simpleProductAttributeCodes);
        if (true === empty($childrenArray)) {
            return false;
        }
        $attributesArray = $this->_generateConfigurableAttributesArray($configProductTypeInstance, $childrenArray);
        if (true === empty($attributesArray)) {
            return false;
        }

        foreach ($productData['children'] as $productDataSku) {
            /** @var $productModel Mage_Catalog_Model_Product **/
            $productModel = Mage::getModel('catalog/product')->loadByAttribute('sku', $productDataSku);

            if (null === $productModel->getId()) {
                continue;
            }

            $configProduct->setData('manufacturer', $productModel->getData('manufacturer'));
            break;
        }

        $configProduct->setConfigurableProductsData($childrenArray);
        $configProduct->setConfigurableAttributesData($attributesArray);
        $this->_setConfigProductStockData($configProduct, $productData);
        $this->_assignImagesOfFirstChild($configProduct, $productData);

        return $configProduct;
    }

    /**
     * @param array $productData
     *
     * @return array
     */
    private function _getSimpleProductConfigurableAttributes(array $productData)
    {
        $productAttributeCodes = [];
        $defaultStoreId = Mage::app()->getWebsite()->getDefaultGroup()->getDefaultStoreId();
        $configurableAttributesCodes = $this->_getConfigurableAttributes();
        foreach ($configurableAttributesCodes as $configurableAttribute) {
            $code = $configurableAttribute->getAttributeCode();
            foreach ($productData['children'] as $child) {
                $productModel = Mage::getModel('catalog/product')->loadByAttribute('sku', $child);
                if (false === $productModel || null === $productModel->getId()) {
                    continue;
                }
                $productId = $productModel->getId();
                $value = Mage::getResourceModel('catalog/product')
                    ->getAttributeRawValue($productId, $code, $defaultStoreId);
                if (false != $value) {
                    $productAttributeCodes[$code] = $code;
                }
            }
        }

        return $productAttributeCodes;
    }

    /**
     * @param array $productData
     *
     * @return Mage_Catalog_Model_Product
     */
    private function _createConfigurableProduct(array $productData)
    {
        $configProduct = Mage::getModel('catalog/product');
        /** @var  $configProduct Mage_Catalog_Model_Product */
        $productDataArray = [
            'website_ids' => $productData['website_ids'],
            'attribute_set_id' => $this->_getDefaultAttributeSetId(),
            'type_id' => Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE,
            'created_at' => Mage::getSingleton('core/date')->gmtDate(),
            'sku' => $productData['sku'],
            'name' => $productData['name'],
            'status' => (int)$productData['status'],
            'tax_class_id' => $productData['tax_class_id'],
            'visibility' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH
        ];
        $configProduct->addData($productDataArray);

        if (true === isset($productData['manufacturers'])) {
            $configProduct->setManufacturers(
                $this->getInternalManufacturerIds($productData['manufacturers'])
            );
            $configProduct->setData(
                'manufacturer_ids', $this->getInternalManufacturerIds($productData['manufacturers'])
            );
        }
        if (true === isset($productData['categories']) && false === empty($productData['categories'])) {
            $configProduct->setCategoryIds(
                $this->getInternalCategoryIds($productData['categories'])
            );
        }
        if (true === isset($productData['price'])) {
            $configProduct->setData('price', $productData['price']);
        }
        if (true === isset($productData['description'])) {
            $configProduct->setData('description', $productData['description']);
        }
        if (true === isset($productData['short_description'])) {
            $configProduct->setData('short_description', $productData['short_description']);
        }

        $configProduct->setData('can_save_configurable_attributes', true)
            ->setData('can_save_custom_options', true)
            ->setData('is_imported', true);

        return $configProduct;
    }

    /**
     * @param array $productData
     *
     * @return array
     */
    private function _setConfigurableProductPrice(array $productData)
    {
        $prices = [];

        foreach ($productData['children'] as $child) {
            /* @var $catalogProductModel Mage_Catalog_Model_Product */
            $catalogProductModel = Mage::getModel('catalog/product');
            /* @var $childProductModel Mage_Catalog_Model_Product */
            $childProductModel = $catalogProductModel->load($catalogProductModel->getIdBySku($child));

            if (null !== $childProductModel->getId()) {
                $prices[$childProductModel->getPrice()] = $childProductModel->getPrice();
            }
        }

        sort($prices);

        if (true === isset($prices[0])) {
            $productData['price'] = $prices[0];
        }

        return $productData;
    }

    /**
     * @return Mage_Eav_Model_Entity_Attribute[]
     */
    private function _getConfigurableAttributes()
    {
        if (!is_array($this->configurableAttributes)) {
            $this->configurableAttributes = $this->attributeLoader
                ->loadAttributes($this->configurableAttributeCodes);
        }

        return $this->configurableAttributes;
    }

    /**
     * @param Mage_Catalog_Model_Product_Type_Configurable $configProductTypeInstance
     * @param array $childrenArray
     *
     * @return array
     */
    private function _generateConfigurableAttributesArray(
        Mage_Catalog_Model_Product_Type_Configurable $configProductTypeInstance, $childrenArray)
    {
        $attributesArray = $configProductTypeInstance->getConfigurableAttributesAsArray();
        foreach ($attributesArray as $key => $attribute) {
            foreach ($childrenArray as $childId => $childArray) {
                $attributesArray[$key]['values'][$childId] = $this
                    ->_generateConfigurableAttributesArrayForChildren($childArray, $attribute);
            }
        }

        return $attributesArray;
    }

    /**
     * @param array $childArray
     * @param array $attribute
     *
     * @return array
     */
    private function _generateConfigurableAttributesArrayForChildren(array $childArray, array $attribute)
    {
        $attributesArray = [];
        foreach ($childArray as $child) {
            if ($child['attribute_id'] === $attribute['attribute_id']) {
                $attributesArray = $child;
            }
        }
        return $attributesArray;
    }

    /**
     * @param array $productData
     * @param array $simpleProductAttributeCodes
     *
     * @return array
     */
    private function _generateChildrenArray(array $productData, array $simpleProductAttributeCodes)
    {
        $childrenArray = [];
        $configurableAttributes = $this->_getConfigurableAttributes();

        foreach ($configurableAttributes as $configurableAttributeCode => $configurableAttribute) {
            if (false === in_array($configurableAttributeCode, $simpleProductAttributeCodes)) {
                unset($configurableAttributes[$configurableAttributeCode]);
            }
        }

        foreach ($productData['children'] as $child) {
            $childrenArray = $this->_generateSingleChildArray(
                $child, $configurableAttributes, $childrenArray
            );
        }

        return $childrenArray;
    }

    /**
     * @param string $childSku
     * @param array  $configurableAttributes
     * @param array  $childrenArray
     *
     * @return array
     */
    private function _generateSingleChildArray(
        $childSku,
        array $configurableAttributes,
        array $childrenArray
    )
    {
        /* @var $childProductModel Mage_Catalog_Model_Product */
        $childProductModel = Mage::getModel('catalog/product');
        $childProductModel->load($childProductModel->getIdBySku($childSku));

        if (null !== $childProductModel->getId()) {
            $childProductModel->setData('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
            $childProductModel->save();
            $childrenArray[$childProductModel->getId()] = [];

            foreach ($configurableAttributes as $attributeKey => $attribute) {
                $childrenArray[$childProductModel->getId()][$attributeKey] = [
                    'label'            => $attribute->getName(),
                    'attribute_id'     => $attribute->getId(),
                    'value_index'      => $childProductModel->getData($attribute->getAttributeCode()),
                    'is_percent'       => 0,
                    'pricing_value'    => 0,
                ];
            }
        }

        return $childrenArray;
    }

    /**
     * @return int
     */
    private function _getDefaultAttributeSetId()
    {
        return Mage::getModel('catalog/product')->getDefaultAttributeSetId();
    }

    /**
     * @param Mage_Catalog_Model_Product_Type_Configurable $configProductTypeInstance
     * @param array $simpleProductAttributeCodes
     */
    private function _setUsedProductAttributeIdsForTypeInstance(
        Mage_Catalog_Model_Product_Type_Configurable $configProductTypeInstance,
        array $simpleProductAttributeCodes
    ) {
        $attributes = $this->_getConfigurableAttributes();
        $productAttributeIds = [];
        foreach ($this->configurableAttributeCodes as $code) {
            if (true === isset($simpleProductAttributeCodes[$code])) {
                $productAttributeIds[$code] = $attributes[$code]->getId();
            }
        }
        $configProductTypeInstance->setUsedProductAttributeIds($productAttributeIds);
    }

    /**
     * @param Mage_Catalog_Model_Product $configProduct
     * @param array $productData
     */
    private function _setConfigProductStockData(Mage_Catalog_Model_Product $configProduct, array $productData)
    {
        $configProduct->setStockData([
            'is_in_stock' => (int)$productData['stock_availability'],
            'qty' => self::DEFAULT_QTY_IN_STOCK,
        ]);
    }

    /**
     * @param Mage_Catalog_Model_Product $configProduct
     * @param array $productData
     */
    private function _assignImagesOfFirstChild(Mage_Catalog_Model_Product $configProduct, array $productData)
    {
        if (false === empty($productData['children'])) {
            /* @var $catalogProductModel Mage_Catalog_Model_Product */
            $catalogProductModel = Mage::getModel('catalog/product');

            foreach ($productData['children'] as $childSku) {
                /* @var $childProductModel Mage_Catalog_Model_Product */
                $childProductModel = $catalogProductModel->load($catalogProductModel->getIdBySku($childSku));

                if (null !== $childProductModel->getId()) {
                    $this->_copyProductImages($childProductModel, $configProduct);
                    break;
                }
            }
        }
    }

    /**
     * @param Mage_Catalog_Model_Product $sourceProduct
     * @param Mage_Catalog_Model_Product $destinationProduct
     */
    private function _copyProductImages(
        Mage_Catalog_Model_Product $sourceProduct,
        Mage_Catalog_Model_Product $destinationProduct
    ) {
        $images = [];
        $baseDir = Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();
        foreach ($destinationProduct->getMediaAttributes() as $imageAttribute) {
            /** @var Mage_Catalog_Model_Resource_Eav_Attribute $imageAttribute */
            $imageAttributeCode = $imageAttribute->getAttributeCode();
            $file = $baseDir . $sourceProduct->getData($imageAttributeCode);
            if (file_exists($file)) {
                if (!isset($images[$file])) {
                    $images[$file] = [];
                }
                $images[$file][] = $imageAttributeCode;
            }
        }
        foreach ($images as $file => $imageAttributeList) {
            if (!is_file($file)) {
                continue;
            }
            try {
                $destinationProduct->addImageToMediaGallery($file, $imageAttributeList, false, false);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }

    /**
     * @param Mage_Catalog_Model_Product $configProduct
     */
    private function _saveProduct(Mage_Catalog_Model_Product $configProduct)
    {
        $configProduct->save();
        $productId = $configProduct->getId();
        if ($productId === null) {
            Mage::log(sprintf('ERROR in product: %s', $configProduct->getSku()));
        }
    }
}
