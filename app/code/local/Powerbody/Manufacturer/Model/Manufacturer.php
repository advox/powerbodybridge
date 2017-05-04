<?php

/**
 * Class Powerbody_Manufacturer_Model_Manufacturer
 */
class Powerbody_Manufacturer_Model_Manufacturer extends Mage_Core_Model_Abstract 
{
    /* @var string */
    protected $_eventPrefix = 'manufacturer_manufacturer';

    /* @var Mage_Catalog_Model_Resource_Product_Collection|null */
    protected $_productCollection = null;

    /* @var null */
    protected $_productIds = null;

    /**
     * construct
     */
    protected function _construct() 
    {
        $this->_init('manufacturer/manufacturer');
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function addStoreFilter($storeId)
    {
        $this->setData('filter_by_store_id', $storeId);

        return $this;
    }
    
    /**
     * @return Mage_Catalog_Model_Resource_Product_Collection|null
     */
    public function getProductCollection() 
    {
        if (null === $this->_productCollection) {
            $this->_productCollection = Mage::getSingleton('manufacturer/provider_product')
                ->getProductCollection($this->getId());
        }

        return $this->_productCollection;
    }
    
    /**
     * @param int|null $x
     * @param int|null $y
     *
     * @return string
     */
    public function getResizedImage($x = null, $y = null) 
    {
        $logo = $this->getData('logo');
        if (true !== empty($logo) && ((null !== $x) || (null !== null))) {
            $imagesPath = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA)
                . DS . 'manufacturer' . DS;
            $xString = 'x';
            if (null !== $x) {
                $xString = $x;
            }
            $yString = 'x';
            if (null !== $x) {
                $yString = $y;
            }
            $imageUrl = $imagesPath . $logo;
            $imageChunks = explode('.', $logo);
            $imageFilename = '';
            for ($i = 0; $i < (count($imageChunks) - 1); $i++) {
                $imageFilename .= $imageChunks[$i];
            }
            $imageExtension = $imageChunks[(count($imageChunks) - 1)];
            $imageFilename.='_' . $xString . '_' . $yString;
            $imageFilename.='.' . $imageExtension;
            if (true !== file_exists($imagesPath . 'cache' . DS . $imageFilename)) {
                try {
                    $imageObj = new Varien_Image($imagesPath . $logo);
                    $imageObj->keepAspectRatio(TRUE);
                    $imageObj->resize($x, $y);
                    $imageObj->keepTransparency(false);
                    $imageObj->save($imagesPath . 'cache/' . $imageFilename);
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
            
            return Mage::getBaseUrl('media') . 'manufacturer/cache/' . $imageFilename;
        } elseif ($logo && (null === $x) && (null === $y)) {
            return Mage::getBaseUrl('media') . 'manufacturer/' . $logo;
        }

        return '';
    }

    /**
     * @return string
     */
    public function getUrlKey() 
    {
        if (!parent::getUrlKey()) {
            $this->_initUrlKey();
        }

        return Mage::getBaseUrl() . $this->getData('url_key') . Mage::getStoreConfig('catalog/manufacturer/suffix');
    }

    /**
     * init url key
     */
    protected function _initUrlKey()
    {
        $this->setData('url_key', $this->_getManufacturerStoreModel()->getData('url_key'));
    }

    /**
     * @return Powerbody_Manufacturer_Model_Manufacturer_Store|null
     */
    protected function _getManufacturerStoreModel()
    {
        return Mage::getModel('manufacturer/manufacturer_store')->getCollection()
            ->addFieldToFilter(
                'store_id',
                Powerbody_Manufacturer_Model_Manufacturer_Store::DEFAULT_MANUFACTURER_STORE_ID
            )
            ->addFieldToFilter('manufacturer_id', $this->getId())
            ->getFirstItem();
    }
}
