<?php

/**
 * Class Powerbody_Bridge_Model_Dropshipping_Order
 */
class Powerbody_Bridge_Model_Dropshipping_Order extends Powerbody_Bridge_Model_Dropshipping_Abstract
{
    const SECONDS_IN_DAY = 86400;
    const DAYS = 7;
    const STATUS_SUCCESS = 'SUCCESS';
    const STATUS_FAIL = 'FAIL';

    /**
     * @param Mage_Sales_Model_Order $salesOrderModel
     *
     * @throws Exception
     */
    public function createOrder(Mage_Sales_Model_Order $salesOrderModel)
    {
        /* @var $dropshippingApi Dropshipping_Api */
        $dropshippingApi = new Dropshipping_Api($this->_getApiConfigData());
        $orderDataArray = $this->_prepareOrderData($salesOrderModel);
        $response = $dropshippingApi->createOrder($orderDataArray);
        $this->_updateBridgeOrderTransferStatus($salesOrderModel, $response);
    }

    /**
     * process creating orders
     */
    public function processCreatingOrders()
    {
        if (false === $this->_getHelper()->getIsEnabled()) {
            return;
        }

        /* @var $orderCollection Varien_Data_Collection */
        $orderCollection = $this->_getSalesOrderCollectionToProcessing();

        if (0 === $orderCollection->getSize()) {
            return false;
        }

        /* @var $salesOrderModel Mage_Sales_Model_Order */
        foreach ($orderCollection as $salesOrderModel) {
            try {
                $this->createOrder($salesOrderModel);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }
    
    /**
     * @param Mage_Sales_Model_Order $salesOrderModel
     *
     * @return array
     */
    public function placeDropshippingOrder(Mage_Sales_Model_Order $salesOrderModel)
    {
        /* @var $dropshippingApi Dropshipping_Api */
        $dropshippingApi = new Dropshipping_Api($this->_getApiConfigData());
        $orderDataArray = $this->_prepareOrderData($salesOrderModel);
        $orderDataArray = array_merge($orderDataArray, ['is_placed' => true]);

        return $dropshippingApi->updateOrder($orderDataArray);
    }
    
    /**
     * update orders
     */
    public function updateOrders()
    {
        /* @var $dropshippingApi Dropshipping_Api */
        $dropshippingApi = new Dropshipping_Api($this->_getApiConfigData());
        $requestData = [
            'from'  => date('Y-m-d', $this->_getRequestTimestamp()),
        ];

        $orders = [];
        try {
            $orders = $dropshippingApi->getOrdersForDropclient($requestData);
        } catch (Exception $e) {
            Mage::logException($e);
        }

        foreach ($orders as $order) {
            if (false === isset($order['order_id'])) {
                continue;
            }

            /* @var $salesOrderModel Mage_Sales_Model_Order */
            $salesOrderModel = Mage::getModel('sales/order')->load($order['order_id']);

            if (null !== $salesOrderModel->getId()
                && true === isset($order['status'])
                && true === isset($order['state'])
            ) {
                $salesOrderModel->setData('status', $order['status']);
                $salesOrderModel->setData('state', $order['state']);
                $salesOrderModel->getResource()->saveAttribute($salesOrderModel, 'state');
                $salesOrderModel->getResource()->saveAttribute($salesOrderModel, 'status');
            }
        }
    }
    
    /**
     * @return Varien_Data_Collection
     */
    protected function _getSalesOrderCollectionToProcessing()
    {
        $orderAvailableStatuses = $this->_getOrderAvailableStatusesToPowerbodyCreateOrder();

        if (true === empty($orderAvailableStatuses)) {
            return new Varien_Data_Collection();
        }

        /* @var $coreResourceModel Mage_Core_Model_Resource */
        $coreResourceModel = Mage::getSingleton('core/resource');

        /* @var $orderCollection Mage_Sales_Model_Resource_Order_Collection */
        $orderCollection = Mage::getModel('sales/order')->getCollection();
        $orderCollection->getSelect()
            ->join(
                [
                    'bots' => $coreResourceModel->getTableName('bridge_order_transfer_status')
                ],
                'bots.order_id = main_table.entity_id'
            );
        $orderCollection
            ->addFieldToFilter('main_table.status', ['in' => $orderAvailableStatuses])
            ->addFieldToFilter(
                'bots.status',
                [
                    'in' => [
                        Powerbody_Bridge_Model_Order_Transfer_Status::STATUS_NOT_PUSH,
                        Powerbody_Bridge_Model_Order_Transfer_Status::STATUS_RESPONSE_FAIL,
                    ]
                ]
            );

        return $orderCollection;
    }

    /**
     * @param Mage_Sales_Model_Order $salesOrderModel
     *
     * @return array
     */
    protected function _prepareOrderData(Mage_Sales_Model_Order $salesOrderModel)
    {
        return [
            'id'                    => $salesOrderModel->getId(),
            'status'                => null,
            'currency_rate'         => $salesOrderModel->getData('base_to_order_rate'),
            'currency'              => $salesOrderModel->getData('order_currency_code'),
            'transport_name'        => $salesOrderModel->getData('shipping_description'),
            'transport_price'       => $salesOrderModel->getData('shipping_incl_tax'),
            'transport_tax'         => $salesOrderModel->getData('shipping_tax_percent'),
            'transport_currency'    => $salesOrderModel->getData('order_currency_code'),
            'transport_code'        => $this->_getHelper()->getShippingMethodId(),
            'weight'                => $salesOrderModel->getData('weight'),
            'date_add'              => $salesOrderModel->getData('created_at'),
            'comment'               => $salesOrderModel->getData('notes'),
            'address'               => $this->_prepareOrderAddressData($salesOrderModel),
            'products'              => $this->_prepareOrderItemsData($salesOrderModel),
        ];
    }

    /**
     * @param Mage_Sales_Model_Order $salesOrderModel
     *
     * @return array
     */
    protected function _prepareOrderAddressData(Mage_Sales_Model_Order $salesOrderModel)
    {
        /* @var $salesOrderAddressModel Mage_Sales_Model_Order_Address */
        $salesOrderAddressModel = $salesOrderModel->getShippingAddress();

        return [
            'name'          => $salesOrderAddressModel->getData('firstname'),
            'surname'       => $salesOrderAddressModel->getData('lastname'),
            'address1'      => $salesOrderAddressModel->getStreet(1),
            'address2'      => $salesOrderAddressModel->getStreet(2),
            'address3'      => $salesOrderAddressModel->getStreet(3),
            'postcode'      => $salesOrderAddressModel->getData('postcode'),
            'city'          => $salesOrderAddressModel->getData('city'),
            'county'        => null,
            'country_name'  => null,
            'country_code'  => $salesOrderAddressModel->getData('country_id'),
            'phone'         => $this->_prepareOrderPhoneNumber($salesOrderAddressModel),
            'email'         => $salesOrderModel->getData('customer_email'),
        ];
    }

    /**
     * @param Mage_Sales_Model_Order $salesOrderModel
     *
     * @return array
     */
    protected function _prepareOrderItemsData(Mage_Sales_Model_Order $salesOrderModel)
    {
        $salesOrderItemsArray = [];

        /* @var $salesOrderItemModel Mage_Sales_Model_Order_Item */
        foreach ($salesOrderModel->getItemsCollection() as $salesOrderItemModel) {
            $salesOrderItemsArray[] = [
                'product_id'    => $salesOrderItemModel->getData('product_id'),
                'name'          => $salesOrderItemModel->getData('name'),
                'sku'           => $salesOrderItemModel->getData('sku'),
                'qty'           => $salesOrderItemModel->getData('qty_ordered'),
                'price'         => $salesOrderItemModel->getData('price_incl_tax'),
                'currency'      => $salesOrderModel->getData('order_currency_code'),
                'tax'           => $salesOrderItemModel->getData('tax_percent'),
            ];
        }

        return $salesOrderItemsArray;
    }

    /**
     * @param Mage_Sales_Model_Order_Address $salesOrderAddressModel
     *
     * @return string
     */
    protected function _prepareOrderPhoneNumber(Mage_Sales_Model_Order_Address $salesOrderAddressModel)
    {
        $telephone = $salesOrderAddressModel->getData('telephone');
        $landlineTelephone = $salesOrderAddressModel->getData('landline_telephone');

        return $telephone ?: $landlineTelephone;
    }

    /**
     * @return int
     */
    private function _getRequestTimestamp()
    {
        /* @var $coreDateModel Mage_Core_Model_Date */
        $coreDateModel = Mage::getModel('core/date');

        return $coreDateModel->timestamp() - (self::SECONDS_IN_DAY * self::DAYS);
    }

    /**
     * @param Mage_Sales_Model_Order $salesOrderModel
     * @param array|null             $response
     */
    private function _updateBridgeOrderTransferStatus(
        Mage_Sales_Model_Order $salesOrderModel,
        array $response = null
    )
    {
        list($status, $responseInfo) = $this->_getStatusAndInfoAfterResponseApi($response);

        /* @var $bridgeOrderTransferStatusModel Powerbody_Bridge_Model_Order_Transfer_Status */
        $bridgeOrderTransferStatusModel = Mage::getModel('bridge/order_transfer_status')
            ->load($salesOrderModel->getId(), 'order_id');
        $bridgeOrderTransferStatusModel
            ->setData('status', $status)
            ->setData('response_info', $responseInfo);
        if (true === isset($response['magento_order_increment_id'])) {
            $bridgeOrderTransferStatusModel->setData(
                'base_order_id',
                $response['magento_order_increment_id']
            );
        }
        $bridgeOrderTransferStatusModel->save();
    }

    /**
     * @param array|null $response
     *
     * @return array
     */
    private function _getStatusAndInfoAfterResponseApi(array $response = null)
    {
        $status = Powerbody_Bridge_Model_Order_Transfer_Status::STATUS_PUSH;
        $responseInfo = '';

        switch (true) {
            case ($this->_isMissingResponse($response)) :
                $status = Powerbody_Bridge_Model_Order_Transfer_Status::STATUS_RESPONSE_FAIL;
                $responseInfo = 'Missing response from API';
                break;

            case ($this->_isFailResponse($response)) :
                $status = Powerbody_Bridge_Model_Order_Transfer_Status::STATUS_RESPONSE_FAIL;
                if (true === isset($response['message'])) {
                    $responseInfo = $response['message'];
                }
                break;

            case (false === $this->_hasMagentoOrderIncrementId($response)) :
                $status = Powerbody_Bridge_Model_Order_Transfer_Status::STATUS_NOT_PUSH;
                $responseInfo = 'Missing "magento_order_increment_id" in response from API';
                break;
        }

        return [$status, $responseInfo];
    }

    /**
     * @param array|null $response
     *
     * @return bool
     */
    private function _isMissingResponse(array $response = null)
    {
        return (false === isset($response['api_response']));
    }

    /**
     * @param array|null $response
     *
     * @return bool
     */
    private function _isFailResponse(array $response = null)
    {
        return (true === isset($response['api_response'])
            && self::STATUS_FAIL === $response['api_response']);
    }

    /**
     * @param array|null $response
     *
     * @return bool
     */
    private function _hasMagentoOrderIncrementId(array $response = null)
    {
        return (true === isset($response['api_response'])
            && self::STATUS_SUCCESS === $response['api_response']
            && true === isset($response['magento_order_increment_id']));
    }

    /**
     * @return array
     */
    private function _getOrderAvailableStatusesToPowerbodyCreateOrder()
    {
        /* @var $configSourceOrderStatusModel Powerbody_Adminhtml_Model_System_Config_Source_Order_Status */
        $configSourceOrderStatusModel = Mage::getModel('powerbody_adminhtml/system_config_source_order_status');

        return $configSourceOrderStatusModel->getOrderAvailableStatusesToPowerbodyCreateOrder();
    }
}
