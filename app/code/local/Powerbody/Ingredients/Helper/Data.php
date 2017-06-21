<?php

/**
 * Class Powerbody_Ingredients_Helper_Data
 */
class Powerbody_Ingredients_Helper_Data extends Mage_Core_Helper_Abstract
{
    const LABEL_IMAGE_PATH = 'labels/image';
    const WATERMARK_IMAGE_PATH = 'labels/watermark';
    const WATERMARK_WIDTH = 300;
    const WATERMARK_HEIGHT = 300;
    const WATERMARK_OPACITY = 10;
    const IMAGE_EXT = '.png';

    /**
     * @param Powerbody_Ingredients_Model_Product_Label $ingredientsProductLabelModel
     */
    public function removeOutdatedLabelImage(
        Powerbody_Ingredients_Model_Product_Label $ingredientsProductLabelModel
    )
    {
        $path = $ingredientsProductLabelModel->getData('path');
        $filename = $ingredientsProductLabelModel->getData('filename');

        if (false === empty($path) && false === empty($filename)) {
            $mediaBaseDir = Mage::getBaseDir('media');
            $originalFilePath = $mediaBaseDir . DS . $path . $filename;
            $cachedFilePath = $mediaBaseDir . DS . $path . 'cached/' . $filename;

            if (true === file_exists($originalFilePath)) {
                unlink($originalFilePath);
            }

            if (true === file_exists($cachedFilePath)) {
                unlink($cachedFilePath);
            }
        }
    }

    /**
     * @param Powerbody_Ingredients_Model_Product_Label $ingredientsProductLabelModel
     * @param array $serviceLabelData
     */
    public function downloadLatestLabelImage(
        Powerbody_Ingredients_Model_Product_Label $ingredientsProductLabelModel,
        array $serviceLabelData
    )
    {
        $baseMediaUrl = Mage::getStoreConfig('bridge_settings/ingredients/labels_default_host');
        $downloadFile = $baseMediaUrl . 'media/' . $serviceLabelData['path'] . $serviceLabelData['filename'];
        $destinationDirectory = Mage::getBaseDir('media') . DS . $serviceLabelData['path'];
        $destinationFile = $destinationDirectory . $ingredientsProductLabelModel->getData('product_id') . self::IMAGE_EXT;

        if (false === file_exists($destinationDirectory)) {
            mkdir($destinationDirectory, 0777, true);
        }

        $curlHandler = curl_init($downloadFile);
        curl_setopt($curlHandler, CURLOPT_NOBODY, true);
        curl_exec($curlHandler);
        $statusCode = (int)curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);
        curl_close($curlHandler);

        if ($statusCode == 200) {
            copy($downloadFile, $destinationFile);
        } else {
            Mage::log(
                $this->__("Can't copy image for product: ") . $ingredientsProductLabelModel->getData('product_id')
            );
        }

    }

    /**
     * @param int $productId
     * @param string $localeCode
     *
     * @return Varien_Object
     */
    public function generateIngredientsProductLabelWithWatermark($productId, $localeCode)
    {
        $watermarkImage = $this->getWatermarkImagePath();
        $mainImage = $this->_getBaseMainImagePath($productId, $localeCode);
        $cachedImagePath = $this->_getBaseCachedImagePath($productId, $localeCode);

        if (false === file_exists($mainImage)) {
            return new Varien_Object(['status' => false]);
        }

        if (false === file_exists($watermarkImage)) {
            return new Varien_Object(['status' => false]);
        }

        try {
            $image = new Varien_Image($mainImage);
            $image->setWatermarkWidth(self::WATERMARK_WIDTH);
            $image->setWatermarkHeigth(self::WATERMARK_HEIGHT);
            $image->keepAspectRatio(true);
            $image->setWatermarkImageOpacity(self::WATERMARK_OPACITY);
            $image->setWatermarkPosition(Varien_Image_Adapter_Abstract::POSITION_CENTER);
            $image->watermark($watermarkImage);
            $image->save($cachedImagePath);

            return new Varien_Object(['status' => true]);

        } catch (Exception $e) {
            Mage::logException($e);
        }

        Mage::app()->cleanCache([Mage_Catalog_Model_Product::CACHE_TAG . '_' . $productId]);

        return new Varien_Object(['status' => false]);
    }

    /**
     * @param Powerbody_Ingredients_Model_Product_Label $ingredientsProductLabelModel
     *
     * @return string
     */
    public function getIngredientsProductLabelImagePath(
        Powerbody_Ingredients_Model_Product_Label $ingredientsProductLabelModel
    )
    {
        $productId = $ingredientsProductLabelModel->getData('product_id');
        $localeCode = $ingredientsProductLabelModel->getData('locale');
        $originalImagePath = $this->_getBaseMainImagePath($productId, $localeCode);
        $cachedImagePath = $this->_getBaseCachedImagePath($productId, $localeCode);
        $imageReturnPath = $this->_getImageReturnUrl($productId, $localeCode);

        if (true === file_exists($cachedImagePath)) {
            return $imageReturnPath;
        }

        if (false === file_exists($cachedImagePath)
            && '' !== $originalImagePath
            && true === file_exists($originalImagePath)
        ) {
            $result = $this->generateIngredientsProductLabelWithWatermark($productId, $localeCode);
            if (true === $result->status && true === file_exists($cachedImagePath)) {
                return $imageReturnPath;
            }
        }

        return '';
    }

    /**
     * @return string
     */
    public function getWatermarkImagePath()
    {
        $watermarkPath = Mage::getStoreConfig('bridge_settings/ingredients/watermark');

        return Mage::getBaseDir('media') . DS . self::WATERMARK_IMAGE_PATH . DS . $watermarkPath;
    }

    /**
     * @param int $productId
     * @param string $localeCode
     *
     * @return string
     */
    protected function _getBaseCachedImagePath($productId, $localeCode)
    {
        $storeId = Mage::app()->getStore()->getId();

        if ($storeId == 0) {
            $storeId = Mage::app()->getDefaultStoreView()->getStoreId();
        }

        $fileName = $productId . self::IMAGE_EXT;
        $cachedFilePath = self::LABEL_IMAGE_PATH . DS . $localeCode .  DS . 'cached' . DS . $storeId;

        return Mage::getBaseDir('media') . DS . $cachedFilePath . DS . $fileName;
    }

    /**
     * @param int $productId
     * @param string $localeCode
     *
     * @return string
     */
    protected function _getBaseMainImagePath($productId, $localeCode)
    {
        $fileName = $productId . self::IMAGE_EXT;

        return Mage::getBaseDir('media') . DS . self::LABEL_IMAGE_PATH . DS . $localeCode .  DS . $fileName;
    }

    /**
     * @param int $productId
     * @param string $localeCode
     *
     * @return string
     */
    protected function _getImageReturnUrl($productId, $localeCode)
    {
        $storeId = Mage::app()->getStore()->getId();
        $fileName = $productId . self::IMAGE_EXT;

        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . self::LABEL_IMAGE_PATH . DS
            . $localeCode . DS . 'cached' . DS . $storeId . DS . $fileName;
    }
}
