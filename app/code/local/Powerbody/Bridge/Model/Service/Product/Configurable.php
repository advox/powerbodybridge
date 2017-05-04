<?php

/**
 * Class Powerbody_Bridge_Model_Service_Product_Configurable
 */
class Powerbody_Bridge_Model_Service_Product_Configurable
{
    public function updateConfigurablePrices()
    {
        /* @var $configurableProvider Powerbody_Bridge_Model_Provider_Product_Configurable */
        $configurableProvider = Mage::getSingleton('bridge/provider_product_configurable');
        $configurableCollection = $configurableProvider->getConfigurableProducts();

        foreach ($configurableCollection as $configurableModel) {
            $lowestPrice = $configurableProvider->getConfigurableLowestPriceFromSimple($configurableModel);
            $configurableModel->setData('price', $lowestPrice);
            $configurableModel->getResource()->saveAttribute($configurableModel, 'price');
        }
    }
}
