<?php

/**
 * Class Powerbody_Ingredients_Block_View
 */
class Powerbody_Ingredients_Block_View extends Mage_Core_Block_Template
{
    const LABEL_IMAGE_PATH = 'labels/img/';

    /**
     * Powerbody_Ingredients_Block_View constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/label/image.phtml');
    }

    /**
     * @return string
     */
    public function getProductLabelImage()
    {
        $currentProduct = Mage::registry('current_product');
        $filename = $this->getLabelImageFilename($currentProduct->getData('entity_id'));
        if ($filename != '') {
            return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)
                . self::LABEL_IMAGE_PATH . DS . $filename;
        }
        return '';
    }

    /**
     * @param int $productId
     * @return string
     */
    public function getLabelImageFilename($productId)
    {
        /* @var Powerbody_Ingredients_Model_Mysql4_Product_Label_Image_Collection $modelLabelImageCollection */
        $modelLabelImageCollection = Mage::getModel('ingredients/product_label_image')
            ->getCollection()
            ->addFilter('product_id', $productId);

        $filename = '';
        if ($modelLabelImageCollection->count() > 0) {
            /* @var Powerbody_Ingredients_Model_Product_Label_Image $modelItem */
            $modelItem = $modelLabelImageCollection->getFirstItem();
            $filename = $modelItem['image'];
        }
        return $filename;
    }

}
