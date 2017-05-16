<?php

/**
 * Class Powerbody_Ingredients_Model_Service_Product_Label
 */
class Powerbody_Ingredients_Model_Service_Product_Label
{
    /**
     * @param Powerbody_Ingredients_Model_Product_Label $ingredientsProductLabelModel
     *
     * @param array $labelServiceData
     */
    public function updateIngredientsProductLabelModel(
        Powerbody_Ingredients_Model_Product_Label $ingredientsProductLabelModel,
        array $labelServiceData
    ) {
        /* @var $catalogProductModel Mage_Catalog_Model_Product */
        $catalogProductModel = Mage::getModel("catalog/product");
        $serviceProductUpdatedAt = $labelServiceData['updated_at'];

        if (null === $ingredientsProductLabelModel->getId()) {
            $ingredientsProductLabelModel->addData([
                'product_id' =>  $catalogProductModel->getIdBySku($labelServiceData['sku']),
                'locale'     =>  $labelServiceData['locale'],
                'status'     =>  Powerbody_Ingredients_Model_Product_Label::STATUS_NEED_UPDATE,
                'created_at' =>  date('Y-m-d H:i:s', time()),
                'updated_at' =>  date('Y-m-d H:i:s', time()),
            ]);
        } else if ($serviceProductUpdatedAt > $ingredientsProductLabelModel->getData('updated_at')) {
            $ingredientsProductLabelModel->addData([
                'status'     =>   Powerbody_Ingredients_Model_Product_Label::STATUS_NEED_UPDATE,
                'updated_at' =>  date('Y-m-d H:i:s', time()),
            ]);
        }
        $ingredientsProductLabelModel->save();
    }

    /**
     * @param Powerbody_Ingredients_Model_Product_Label $ingredientsProductLabelModel
     */
    public function changeIngredientsProductLabelStatus(
        Powerbody_Ingredients_Model_Product_Label $ingredientsProductLabelModel
    ) {
        $ingredientsProductLabelModel->addData(
            [
                'status'     => Powerbody_Ingredients_Model_Product_Label::STATUS_UP_TO_DATE,
                'updated_at' => date('Y-m-d H:i:s', time()),
                'path'       => 'labels/image/' . $ingredientsProductLabelModel->getData('locale') . DS,
                'filename'   => $ingredientsProductLabelModel->getData('product_id').'.png'
            ]
        );
        $ingredientsProductLabelModel->save();
    }
}
