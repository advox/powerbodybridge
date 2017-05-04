<?php

/**
 * Class Powerbody_Bridge_Model_Sync_Product_Images
 */
class Powerbody_Bridge_Model_Sync_Product_Images
{
    const HTTP_CODE_OK = 200;

    /**
     * @var null|Mage_Catalog_Model_Product_Attribute_Media_Api
     */
    private $_mediaApi = null;

    /**
     * @var null|string
     */
    private $_mediaImportDirectory = null;

    /**
     * @var null|Powerbody_Bridge_Model_Service_Logger
     */
    private $_logger = null;

    /**
     * @var array
     */
    private static $mediaAttribute = ['image', 'small_image', 'thumbnail'];

    public function __construct()
    {
        $this->_mediaApi = Mage::getModel('catalog/product_attribute_media_api');
        $this->_mediaImportDirectory = Mage::getBaseDir('media') . DS . 'import';
        $this->_logger = Mage::getModel('bridge/service_logger');
    }

    /**
     * @param Mage_Catalog_Model_Product $productModel
     * @param string                     $imageUrl
     */
    public function addProductImages(Mage_Catalog_Model_Product $productModel, $imageUrl)
    {
        try {
            if ($productModel->getId() === null) {
                return;
            }

            if ($this->_isImageFileExists($imageUrl) === false) {
                return;
            }

            $items = $this->_mediaApi->items($productModel->getId());
            foreach ($items as $item) {
                $this->_mediaApi->remove($productModel->getId(), $item['file']);
            }

            $image = $this->_getImageFromUrl($imageUrl);
            if (empty($image)) {
                return;
            }

            $imagePath = $this->_saveImage($image);
            if (empty($imagePath)) {
                return;
            }
            /* @var $productModel Mage_Catalog_Model_Product */
            $productModel = Mage::getModel('catalog/product')->load($productModel->getId());
            $productModel->addImageToMediaGallery($imagePath, self::$mediaAttribute, false, false);
            $productModel->save();
        } catch (Exception $e) {
            $this->_logger->logException($e);
        }
    }

    /**
     * @param string $path
     * @return bool
     */
    private function _isImageFileExists($path)
    {
        $curlHandler = curl_init($path);
        curl_setopt($curlHandler, CURLOPT_NOBODY, true);
        curl_exec($curlHandler);
        $returnCode = (int) curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);
        curl_close($curlHandler);

        return $returnCode === self::HTTP_CODE_OK;
    }

    /**
     * @param $imageUrl
     * @return string
     */
    private function _getImageFromUrl($imageUrl)
    {
        $curlHandler = curl_init($imageUrl);
        curl_setopt($curlHandler, CURLOPT_HEADER, 0);
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandler, CURLOPT_BINARYTRANSFER, 1);
        $rawData = curl_exec($curlHandler);
        curl_close($curlHandler);

        return $rawData;
    }

    /**
     * @param $image
     * @return string
     */
    private function _saveImage($image)
    {
        if (!file_exists($this->_mediaImportDirectory)) {
            mkdir($this->_mediaImportDirectory, 0777, true);
        }

        $path = $this->_mediaImportDirectory . DS . uniqid(rand(), true) . '.jpg';
        if (file_exists($path)) {
            unlink($path);
        }

        $fileHandler = fopen($path, 'x');
        fwrite($fileHandler, $image);
        fclose($fileHandler);

        return $path;
    }
}
