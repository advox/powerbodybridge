<?php

/**
 * Class Powerbody_Manufacturer_Model_Service_Manufacturer_Margin_Updater
 */
class Powerbody_Manufacturer_Model_Service_Manufacturer_Margin_Updater
{
    /**
     * @var Powerbody_Manufacturer_Model_Service_Product_Price_Updater
     */
    private $_priceUpdater;

    /**
     * @var Powerbody_Manufacturer_Model_Provider_Manufacturer
     */
    private $_manufacturerProvider;


    public function __construct()
    {
        $this->_priceUpdater = Mage::getModel('manufacturer/service_product_price_updater');
        $this->_manufacturerProvider = Mage::getModel('manufacturer/provider_manufacturer');
    }

    /**
     * @param int $manufacturerId
     * @param int $margin
     */
    public function updateMargin($manufacturerId, $margin)
    {
        try {
            $this->_updateManufacturerMargin($manufacturerId, $margin);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * @param array $manufacturerIds
     * @param int|string $margin
     */
    public function massUpdateMargin(array $manufacturerIds, $margin)
    {
        foreach ($manufacturerIds as $id) {
            $this->updateMargin($id, $margin);
        }
    }

    /**
     * @param int $manufacturerId
     * @param int $margin
     * @throws Exception
     */
    private function _updateManufacturerMargin($manufacturerId, $margin)
    {
        $manufacturerModel = $this->_manufacturerProvider->loadById($manufacturerId);
        if ($manufacturerModel->getId() === null) {
            throw new Exception(sprintf('Manufacturer with ID %s not found,', $manufacturerId));
        }

        $manufacturerModel->setData('margin', $margin);
        $manufacturerModel->save();
        $this->_priceUpdater->updateProductsPrices($manufacturerModel);
    }
}
