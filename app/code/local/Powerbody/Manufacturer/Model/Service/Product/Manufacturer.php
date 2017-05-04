<?php
class Powerbody_Manufacturer_Model_Service_Product_Manufacturer
{
    /**
     * @param Mage_Catalog_Model_Product $catalogProductModel
     */
    public function saveProductManufacturerIds(Mage_Catalog_Model_Product $catalogProductModel)
    {
        $productId = $catalogProductModel->getId();

        $oldManufacturers = $this->_getManufacturerProvider()->getProductManufacturerIds($productId);
        $newManufacturers = (array)$catalogProductModel->getData('manufacturer_ids');

        $toInsert = array_diff($newManufacturers, $oldManufacturers);
        $toDelete = array_diff($oldManufacturers, $newManufacturers);

        if (count($toDelete) > 0) {
            $this->_deleteProductManufacturers($toDelete, $productId);
        }

        if (count($toInsert) > 0) {
            $this->_insertProductManufacturers($toInsert, $productId);
        }
    }

    /**
     * @return Mage_Core_Model_Resource
     */
    private function _getCoreResource()
    {
        return Mage::getSingleton('core/resource');
    }

    /**
     * @return Varien_Db_Adapter_Interface
     */
    private function _getAdapter()
    {
        return $this->_getCoreResource()->getConnection('core_write');
    }

    /**
     * @return Powerbody_Manufacturer_Model_Provider_Manufacturer
     */
    private function _getManufacturerProvider()
    {
        return Mage::getSingleton('manufacturer/provider_manufacturer');
    }

    /**
     * @param array $toDelete
     * @param int $productId
     */
    private function _deleteProductManufacturers(array $toDelete, $productId)
    {
        $where = [
            'manufacturer_id IN (?)' => $toDelete,
            'product_id = ?'         => (int) $productId,
        ];
        $this->_getAdapter()->delete($this->_getCoreResource()->getTableName('manufacturer/product'), $where);
    }

    /**
     * @param array $toInsert
     * @param int $productId
     */
    private function _insertProductManufacturers(array $toInsert, $productId)
    {
        $toInsert = $this->_getManufacturerProvider()->getExistentManufacturersIds($toInsert);
        foreach ($toInsert as $manufacturerId) {
            $data[] = [
                'manufacturer_id' => (int) $manufacturerId['id'],
                'product_id'      => (int) $productId,
            ];
        }

        if (isset($data) && !empty($data)) {
            $this->_getAdapter()->insertMultiple($this->_getCoreResource()->getTableName('manufacturer/product'), $data);
        }
    }
}
