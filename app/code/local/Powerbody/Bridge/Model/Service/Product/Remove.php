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
                $where = ['entity_id in (?)' => $productIds];

                $adapter->delete($this->_getCatalogProductTableName(), $where);
                $adapter->delete($this->_getRatingTableName(), $where);
                $adapter->delete($this->_getReviewTableName(), $where);
            }
        } catch (\Exception $e) {
            Mage::logException($e);
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
