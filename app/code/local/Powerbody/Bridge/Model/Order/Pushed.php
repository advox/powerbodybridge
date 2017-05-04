<?php

/**
 * Class Powerbody_Bridge_Model_Order_Pushed
 */
class Powerbody_Bridge_Model_Order_Pushed extends Mage_Core_Model_Abstract
{
    const STATUS_PUSHED = 1;
    const STATUS_PLACED = 2;

    protected function _construct()
    {
        $this->_init('bridge/order_pushed');
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        $adminSessionModel = Mage::getSingleton('admin/session');
        /* @var $adminSessionModel Mage_Admin_Model_Session */
        $adminUserModel = $adminSessionModel->getUser();
        $dataArray = array(
            'admin_user_username' => $adminUserModel ? $adminUserModel->getData('username') : null,
            'status'              => $status,
        );
        $this->addData($dataArray);
        $this->save();
        return $this;
    }
}
