<?php

class Powerbody_Bridge_Model_Cron_Version
{
    /**
     * @var Mage_AdminNotification_Model_Inbox
     */
    private $_messageInbox;

    /**
     * @var Powerbody_Bridge_Model_Service
     */
    private $_apiClient;

    /**
     * @var Powerbody_Bridge_Model_Service_Api_Version_Loader
     */
    private $_apiVersionLoader;

    public function __construct()
    {
        $this->_messageInbox = Mage::getModel('adminnotification/inbox');
        $this->_apiClient = Mage::getSingleton('bridge/service');
        $this->_apiVersionLoader = Mage::getModel('bridge/service_api_version_loader');
    }

    public function notifyAdminIfBridgeIsOutdated()
    {
        $localModuleVersion = $this->_apiVersionLoader->getApiVersion();
        $this->_apiClient->connect();
        $response = $this->_apiClient->callMethod(['method_name' => 'getApiVersion']);
        $this->_apiClient->disconnect();

        if (version_compare($localModuleVersion, $response['api_version']) === -1) {
            $this->_messageInbox->addCritical(
                'Current Powerbody Bridge module version is ' . $response['api_version'] . ', your local version is: ' . $localModuleVersion . '. Please update module to avoid synchronization issues. You can contact ' . $response['contact_person']. ' if you need help upgrading plugin.',
                null,
                'https://github.com/advox/powerbodybridge'
            );
        }
    }
}
