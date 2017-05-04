<?php

/**
 * Class Powerbody_Bridge_Model_Sync_Ingredients
 */
class Powerbody_Bridge_Model_Sync_Ingredients
{
    const LABEL_IMAGE_PATH = 'labels/img/';
    const LABEL_ORIGINALS_PATH = 'media/labels/img/originals/';
    const LINKS_PRODUCTS_CATEGORY_ID = 3;
    const CODE_HTTP_NOT_FOUND = 404;
    const IMAGE_EXTENSION = '.png';

    /* @var int */
    protected $_categoryId = Powerbody_Bridge_Model_Request_Connector::TYPE_INGREDIENTS;

    /* @var string */
    protected $_serviceMethod = 'getIngredientsLabelImage';

    /**
     * @param int $id
     * @param array $itemData
     * @throws \Mage_Core_Exception
     */
    protected function _saveItem($id, $itemData)
    {
        /* @var Powerbody_Bridge_Model_Mysql4_Links_Collection $linkCollection */
        $linkCollection = Mage::getModel('bridge/links')->getCollection()
            ->addFilter('category', self::LINKS_PRODUCTS_CATEGORY_ID)
            ->addFilter('base_id', $itemData['product_id']);

        if ($linkCollection->count() > 0) {
            $sourceHost = Mage::getStoreConfig('bridge_settings/ingredients/labels_default_host');
            $sourceImage = $sourceHost . DS . self::LABEL_ORIGINALS_PATH . $itemData['image'];
            $downloadPath = Mage::getBaseDir('media') . DS . self::LABEL_IMAGE_PATH . DS;
            /* @var Powerbody_Bridge_Model_Links $linkCollectionItem */
            $linkCollectionItem = $linkCollection->getFirstItem();
            $newProductId = $linkCollectionItem['new_id'];
            if (false === file_exists($downloadPath)) {
                if (false === mkdir($downloadPath, 0777, true)) {
                    throw new Mage_Core_Exception(
                        Mage::helper('bridge')->__('Failed to create folders')
                    );
                }
            }
            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $sourceImage);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($httpCode == self::CODE_HTTP_NOT_FOUND) {
                    curl_close ($ch);
                    $result = 0;
                } else {
                    $data = curl_exec ($ch);
                    $file = fopen($downloadPath . $newProductId . self::IMAGE_EXTENSION, "w+");
                    $result = fputs($file, $data);
                    fclose($file);
                }
                if ($result > 0) {
                    /* @var Powerbody_Ingredients_Model_Mysql4_Product_Label_Image_Collection $searchModel */
                    $searchModel = Mage::getModel('ingredients/product_label_image')->getCollection()
                        ->addFilter('product_id', $newProductId);
                    /* @var Powerbody_Ingredients_Model_Product_Label_Image $searchModelItem */
                    $searchModelItem = $searchModel->getFirstItem();
                    $itemId = null;
                    if ($searchModelItem->getId() != null) {
                        $itemId = $searchModelItem->getId();
                    }
                    $modelData = array(
                        'id'            =>  $itemId,
                        'product_id'    =>  $newProductId,
                        'image'         =>  $newProductId . self::IMAGE_EXTENSION,
                        'date_modified' =>  date('Y-m-d H:i:s', time()),
                    );
                    $searchModelItem->setData($modelData);
                    $searchModelItem->save();
                }
            } catch (Exception $e) {
                throw new Mage_Core_Exception(
                    Mage::helper('bridge')->__('CURL error')
                );
            }
        }
    }
}
