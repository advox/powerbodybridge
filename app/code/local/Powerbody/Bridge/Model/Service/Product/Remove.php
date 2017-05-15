<?php

/**
 * Class Powerbody_Bridge_Model_Service_Product_Remove
 */
class Powerbody_Bridge_Model_Service_Product_Remove
{
    const CHUNK_SIZE = 300;

    /**
     * @param array $productsIds
     */
    public function removeProducts(array $productsIds)
    {
        $adapter = $this->_getAdapter();
        $productIdsChunk = array_chunk($productsIds, self::CHUNK_SIZE);
       
        try {
            foreach ($productIdsChunk as $productIds) {
                $this->disableProductsByProductIdsArray($productIds);
            }
        } catch (\Exception $e) {
            Mage::logException($e);
        }
    }
    
    /**
     * @param array $skuArray
     */
    public function disableProductsByProductIdsArray(array $productIdsArray)
    {
        $productCollection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addFieldToFilter('entity_id', ['in' => $productIdsArray]);
        /* @var $productCollection Mage_Catalog_Model_Resource_Product_Collection */
        
        foreach ($productCollection as $productModel) {
            /* @var $productModel Mage_Catalog_Model_Product */
            $productModel->setWebsiteIds([]);
            $productModel->setData('status', Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
            $productModel->save();
        }
    }
    /**
     * @return Varien_Db_Adapter_Interface
     */
    private function _getAdapter()
    {
        return Mage::getModel('core/resource')->getConnection('catalog_write');
    }

    /**
     * @return mixed
     */
    private function _getCatalogProductTableName()
    {
        return Mage::getModel('catalog/product')->getResource()->getEntityTable();
    }

    /**
     * @return string
     */
    private function _getRatingTableName()
    {
        return Mage::getModel('rating/rating')->getResource()->getMainTable();
    }

    /**
     * @return string
     */
    private function _getReviewTableName()
    {
        return Mage::getModel('review/review')->getResource()->getMainTable();
    }
}
