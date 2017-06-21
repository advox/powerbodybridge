<?php

/**
 * Class Powerbody_Bridge_Model_Sync_Product_Label
 */
class Powerbody_Bridge_Model_Sync_Product_Label extends Powerbody_Bridge_Model_Sync_Abstract
{
    const SERVICE_METHOD = 'getIngredientsLabelData';
    const IMPORT_PARTITION_SIZE = 500;

    /* @var Powerbody_Ingredients_Model_Provider_Product_Label */
    private $_productLabelProvider;

    /* @var Powerbody_Ingredients_Model_Service_Product_Label */
    private $_productLabelService;

    /* @var Powerbody_Ingredients_Helper_Data */
    private $_ingredientsHelper;

    /**
     * Powerbody_Bridge_Model_Sync_Product_Label constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->_productLabelProvider = Mage::getSingleton('ingredients/provider_product_label');
        $this->_productLabelService = Mage::getSingleton('ingredients/service_product_label');
        $this->_ingredientsHelper = Mage::helper('ingredients');
    }

    /**
     * @inheritdoc
     */
    public function processImport()
    {
        $skuArray = $this->_getProductProvider()->getImportedProductsToUpdateSkuArray();

        if (empty($skuArray)) {
            return;
        }

        $skuArray = array_chunk($skuArray, self::IMPORT_PARTITION_SIZE);

        foreach ($skuArray as $skuPartialArray) {
            $productLabelArray = $this->_getProductsLabelArray(
                [
                    'sku'    => $skuPartialArray,
                    'locale' => Mage::app()->getLocale()->getLocaleCode()
                ]
            );

            if (true === $this->_checkResponseArrayIsValid($productLabelArray)) {
                $this->_updateIngredientsProductLabelData($productLabelArray);
            }
        }
    }

    /**
     * @param array $dataArray
     *
     * @return array
     */
    protected function _getProductsLabelArray(array $dataArray)
    {
        return $this->_makeServiceMethodRequest(self::SERVICE_METHOD, $dataArray);
    }

    /**
     * @param array $productLabelArray
     */
    protected function _updateIngredientsProductLabelData(array $productLabelArray)
    {
        foreach ($productLabelArray['data'] as $localeCode => $labelDataArray) {
            $this->_updateIngredientsProductLabelForLocale($labelDataArray, $localeCode);
        }
    }

    /**
     * @param array $labelDataArray
     * @param string $localeCode
     */
    protected function _updateIngredientsProductLabelForLocale(
        array $labelDataArray, $localeCode
    ) {
        /* @var $resourceModel Mage_Core_Model_Resource */
        $resourceModel = Mage::getSingleton('core/resource');
        /* @var $connection Magento_Db_Adapter_Pdo_Mysql */
        $connection = $resourceModel->getConnection('core_write');

        foreach ($labelDataArray as $sku => $labelData) {
            $connection->beginTransaction();
            try {
                /* @var $ingredientsProductLabelModel Powerbody_Ingredients_Model_Product_Label */
                $ingredientsProductLabelModel = $this->_productLabelProvider
                    ->getIngredientsProductLabelModelByLocaleAndSku($localeCode, $labelData);
                $this->_productLabelService
                    ->updateIngredientsProductLabelModel($ingredientsProductLabelModel, $labelData);
                $this->_downloadLabelImage($ingredientsProductLabelModel, $labelData);
                $connection->commit();
            } catch (Exception $e) {
                $connection->rollBack();
                Mage::logException($e);
            }

        }
    }

    /**
     * @param Powerbody_Ingredients_Model_Product_Label $ingredientsProductLabelModel
     * @param array $serviceLabelData
     */
    protected function _downloadLabelImage(
        Powerbody_Ingredients_Model_Product_Label $ingredientsProductLabelModel, array $serviceLabelData
    ) {
        $this->_ingredientsHelper->removeOutdatedLabelImage($ingredientsProductLabelModel);
        $this->_ingredientsHelper->downloadLatestLabelImage($ingredientsProductLabelModel, $serviceLabelData);
        $this->_ingredientsHelper->generateIngredientsProductLabelWithWatermark(
            $ingredientsProductLabelModel->getData('product_id'),
            $ingredientsProductLabelModel->getData('locale')
        );
        $this->_productLabelService->changeIngredientsProductLabelStatus($ingredientsProductLabelModel);
    }
}
