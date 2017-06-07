<?php

/**
 * Class Powerbody_Bridge_Model_Sync_Product_Qty
 */
class Powerbody_Bridge_Model_Sync_Product_Qty extends Powerbody_Bridge_Model_Sync_Abstract
{
    const IMPORT_PARTITION_SIZE = 500;
    const SERVICE_METHOD = 'getProductsQty';
    const DEFAULT_QUANTITY = 0.00;

    public function processImport()
    {
        $sku = $this->_getProductProvider()->getImportedProductsSKUArray();

        if (empty($sku)) {
            return;
        }

        $sku = array_chunk($sku, self::IMPORT_PARTITION_SIZE);
        $quantities = [];
        foreach ($sku as $skuPartialArray) {
            $productsQtyArray = $this->_getProductQtyArray(['sku' => $skuPartialArray, 'all_products' => true]);
            if ($productsQtyArray === null) {
                continue;
            }
            $quantities = array_merge($quantities, $productsQtyArray);
        }

        if (empty($quantities)) {
            return;
        }

        $this->_saveQuantities($quantities);
    }
    
    /**
     * 
     * @param array $params
     * 
     * @return array|null
     */
    protected function _getProductQtyArray(array $params = [])
    {
        return $this->_makeServiceMethodRequest(self::SERVICE_METHOD, $params);
    } 
    
    /**
     * @param array $productsArray
     */
    protected function _saveQuantities(array $productsArray)
    {
        foreach ($productsArray as $productArray) {
            if (!isset($productArray['sku'])) {
                continue;
            }

            $qty = self::DEFAULT_QUANTITY;
            if (isset($productArray['qty'])) {
                $qty = $this->_formatQty($productArray['qty']);
            }

            $productModel = $this->_getProductModel();
            $productId = $productModel->getIdBySku($productArray['sku']);
            if ($productId === false) {
                return;
            }

            $stockItem = $this->_getProductsStockItem($productId);
            $stockItem->setData('product_id', $productId);
            $stockItem->setData('use_config_manage_stock', false);
            $stockItem->setData('manage_stock', true);
            $stockItem->setData('stock_id', 1);
            $stockItem->setData('qty', $qty);
            
            if ($stockItem->getData('qty') != $stockItem->getOrigData('qty')) {
                $stockItem->save();
            }
        }
    }

    /**
     * @param int $productId
     * @return Mage_CatalogInventory_Model_Stock_Item
     */
    protected function _getProductsStockItem($productId)
    {
        return Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
    }

    /**
     * @param string|int|float $qty
     * @return string
     */
    private function _formatQty($qty)
    {
        return number_format(floatval($qty), 2);
    }
}
