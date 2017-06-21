<?php

/**
 * Class Powerbody_Ingredients_LabelController
 */
class Powerbody_Ingredients_LabelController extends Mage_Core_Controller_Front_Action
{
    /**
     * @return string
     */
    public function indexAction()
    {
        $productId = $this->getRequest()->getParam('productId');
        $localeCode = Mage::app()->getLocale()->getLocaleCode();
        /* @var $ingredientsLabelProvider Powerbody_Ingredients_Model_Provider_Product_Label */
        $ingredientsLabelProvider = Mage::getSingleton('ingredients/provider_product_label');
        /* @var $ingredientsHelper Powerbody_Ingredients_Helper_Data */
        $ingredientsHelper = Mage::helper('ingredients');

        $ingredientProductLabelModel = $ingredientsLabelProvider
            ->getIngredientProductLabelModelForProductAndLocaleCode($productId, $localeCode);

        $response = ['success' => false];

        if ($ingredientProductLabelModel->getData('status') == Powerbody_Ingredients_Model_Product_Label::STATUS_UP_TO_DATE) {
            $response = [
                'success' => true,
                'data'    => $ingredientsHelper->getIngredientsProductLabelImagePath($ingredientProductLabelModel)
            ];
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');

        return $this->getResponse()->setBody(json_encode($response));
    }
}
