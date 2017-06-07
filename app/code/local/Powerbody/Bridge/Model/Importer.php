<?php

/**
 * Class Powerbody_Bridge_Model_Importer
 */
class Powerbody_Bridge_Model_Importer
{
    /** @var array */
    private static $_servicesFull = [
        /** @see Powerbody_Bridge_Model_Sync_Attribute */
        'bridge/sync_attribute',

        /** @see Powerbody_Bridge_Model_Sync_Product */
        'bridge/sync_product',
    ];

    /** @var array */
    private static $_servicesPartial = [
        /** @see Powerbody_Bridge_Model_Sync_Product_Qty */
        'bridge/sync_product_qty',

        /** @see Powerbody_Bridge_Model_Sync_Product_Price */
        'bridge/sync_product_price',
    ];

    /**
     * import attributes and products
     */
    public function importAttributesAndProducts()
    {
        $this->_runImport(self::$_servicesFull);
    }

    /**
     * import stocks
     */
    public function importStocks()
    {
        $this->_runImport([self::$_servicesPartial[0]]);
    }
    
    /**
     * import prices
     */
    public function importPrices() {
        $this->_runImport([self::$_servicesPartial[1]]);
    }

    /**
     * @param array $servicesArray
     */
    private function _runImport(array $servicesArray)
    {
        foreach ($servicesArray as $serviceClass) {
            try {
                $service = $this->_instantiateService($serviceClass);
                $service->processImport();
            } catch (LogicException $e) {
                Mage::logException($e);
                return;
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }

    /**
     * @param string $serviceClass
     *
     * @return Powerbody_Bridge_Model_Sync_Abstract
     *
     * @throws LogicException
     */
    private function _instantiateService($serviceClass)
    {
        $service = Mage::getModel($serviceClass);
        if (!is_object($service)) {
            throw new LogicException(
                sprintf('Service %s not found.', $serviceClass)
            );
        }

        if (!$service instanceof Powerbody_Bridge_Model_Sync_Abstract) {
            throw new LogicException(
                sprintf('Services should extend Powerbody_Bridge_Model_Sync_Abstract, check %s', get_class($service))
            );
        }

        return $service;
    }
}
