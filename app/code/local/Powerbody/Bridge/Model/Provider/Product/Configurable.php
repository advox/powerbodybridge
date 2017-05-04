<?php

/**
 * Class Powerbody_Bridge_Model_Provider_Product_Configurable
 */
class Powerbody_Bridge_Model_Provider_Product_Configurable
{
    /**
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getConfigurableProducts()
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $productCollection = Mage::getResourceModel('catalog/product_collection');
        $productCollection->addAttributeToSelect('type_id')
            ->addAttributeToFilter('type_id', Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE);

        return $productCollection;
    }

    /**
     * @param $configurableModel
     *
     * @return mixed
     */
    public function getConfigurableLowestPriceFromSimple($configurableModel)
    {
        $childProducts = Mage::getModel('catalog/product_type_configurable')
            ->getUsedProducts(null, $configurableModel);
        $pricesArray = [];

        foreach ($childProducts as $childProduct) {
            $pricesArray[$childProduct->getData('price')] = $childProduct->getData('price');
        }

        sort($pricesArray);

        if (true === isset($pricesArray[0])) {
            return $pricesArray[0];
        }

        return 0;
    }
}
