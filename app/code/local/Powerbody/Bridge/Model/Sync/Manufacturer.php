<?php

/**
 * Class Powerbody_Bridge_Model_Sync_Manufacturer
 */
class Powerbody_Bridge_Model_Sync_Manufacturer
{
    const DIR_MEDIA_MANUFACTURER = 'manufacturer';

    /* @var string */
    protected $_serviceMethod = 'getManufacturers';

    /* @var string */
    protected $_idField = 'id';

    /* @var Powerbody_Bridge_Helper_File|null */
    protected $_fileHelper = null;

    /* @var Powerbody_Bridge_Model_Service|null */
    protected $_service = null;

    /* @var array */
    protected $_serviceParams = [];

    /* @var array */
    protected $_unusedDataFields = [];

    /**
     * construct
     */
    public function __construct()
    {
        $this->_service = Mage::getSingleton('bridge/service');
        $this->_fileHelper = Mage::helper('bridge/file');
        $this->_unusedDataFields = array('store_id', 'id', 'related_manufacturer_ids');
    }

    /**
     * @param array $manufacturerIds
     */
    public function processManufacturersByIds(array $manufacturerIds)
    {
        $this->_getService()->connect();
        /* @var $syncModel Powerbody_Bridge_Model_Sync_Manufacturer */
        $syncModel = Mage::getModel('bridge/sync_manufacturer');
        $syncModel->setServiceParams([
                'manufacturers' => $manufacturerIds,
        ]);
        $syncModel->processImport();
        $this->_getService()->disconnect();
    }

    /**
     * @param array $params
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setServiceParams(array $params)
    {
        $this->_serviceParams = $params;

        return $this;
    }

    /**
     * Process import items.
     */
    public function processImport()
    {
        $itemsDataArray = $this->_getItemsDataArray();
        if (true === empty($itemsDataArray)) {
            return;
        }
        $this->saveItems($itemsDataArray);
    }


    /**
     * @return array
     *
     * @throws Exception
     */
    protected function _getItemsDataArray()
    {
        $dataArray = [];
        $this->_getService()->connect();

        try {
            $dataArray = $this->_getService()->callMethod([
                'json_data'     => Mage::helper('core')->jsonEncode($this->_serviceParams),
                'method_name'   => $this->_serviceMethod
            ]);
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::log($e->getMessage());
        }

        return $dataArray;
    }

    /**
     * @param array $itemsDataArray
     */
    public function saveItems(array $itemsDataArray)
    {
        foreach ($itemsDataArray as $itemData) {
            if (!isset($itemData[$this->_idField])) {
                continue;
            }
            $baseId = $itemData[$this->_idField];
            $itemData = $this->_removeNotUsedDataFields($itemData);
            $id = $this->_getManufacturerNewId($baseId);
            $modelId = $this->_saveItem($id, $itemData);
            $this->_assignManufacturerToImportedManufacturer($baseId, $modelId);
        }
    }

    /**
     * @param int $baseId
     * @param int $manufacturerId
     */
    protected function _assignManufacturerToImportedManufacturer($baseId, $manufacturerId)
    {
        /* @var $importedManufacturerModel Powerbody_Bridge_Model_Imported_Manufacturer */
        $importedManufacturerModel = Mage::getModel('bridge/imported_manufacturer')->load($baseId, 'base_manufacturer_id');
        $importedManufacturerModel->setData('client_manufacturer_id', $manufacturerId);
        $importedManufacturerModel->save();
    }

    /**
     * @param int $baseId
     *
     * @return string|null
     */
    protected function _getManufacturerNewId($baseId)
    {
        if (null !== $baseId) {
            /* @var $importedManufacturerModel Powerbody_Bridge_Model_Imported_Manufacturer */
            $importedManufacturerModel = Mage::getModel('bridge/imported_manufacturer')->load($baseId, 'base_manufacturer_id');
            return $importedManufacturerModel->getData('client_manufacturer_id');
        }
        return null;
    }

    /**
     * @param array $itemData
     *
     * @return array
     */
    protected function _removeNotUsedDataFields($itemData)
    {
        foreach ($this->_unusedDataFields as $toRemove) {
            unset($itemData[$toRemove]);
        }
        return $itemData;
    }

    /**
     * @param Powerbody_Manufacturer_Model_Manufacturer $manufacturerModel
     *
     * @return bool
     */
    protected function _removeOldImage(Powerbody_Manufacturer_Model_Manufacturer $manufacturerModel)
    {
        $logoDirPath = Mage::getBaseDir('media') . DS . self::DIR_MEDIA_MANUFACTURER;
        $normalLogoDirPath = $logoDirPath . DS . 'normal';
        $logoFilePath = $logoDirPath . DS . $manufacturerModel->getData('logo');
        $normaLogoFilePath = $normalLogoDirPath . DS . $manufacturerModel->getData('logo_normal');

        $removeLogoResult = $this->_fileHelper->removeFile($logoFilePath);
        $removeNormalLogoResult = $this->_fileHelper->removeFile($normaLogoFilePath);

        return $removeLogoResult && $removeNormalLogoResult;
    }

    /**
     * @param int   $id
     * @param array $itemData
     *
     * @return int|null
     */
    protected function _saveItem($id, $itemData)
    {
        try {
            /* @var $manufacturerModel Powerbody_Manufacturer_Model_Manufacturer */
            $manufacturerModel = $this->_getManufacturerModel($id);

            if ($manufacturerModel->getId()) {
                $this->_removeOldImage($manufacturerModel);
            }
            $manufacturerModel->addData($itemData);
            $manufacturerModel->setStoreIds([Powerbody_Manufacturer_Model_Manufacturer_Store::DEFAULT_MANUFACTURER_STORE_ID]);
            $manufacturerModel->save();

            $this->_importManufacturerImages($itemData);

            return $manufacturerModel->getId();
        } catch (Exception $e) {
            Mage::log($e->getMessage());
            Mage::logException($e);
        }

        return null;
    }

    /**
     * @return Powerbody_Bridge_Helper_File
     */
    protected function _getFileHelper()
    {
        return $this->_fileHelper;
    }

    /**
     * @param int|null $manufacturerId
     *
     * @return Powerbody_Manufacturer_Model_Manufacturer
     */
    protected function _getManufacturerModel($manufacturerId = null)
    {
        /* @var $manufacturerModel Powerbody_Manufacturer_Model_Manufacturer */
        $manufacturerModel = Mage::getModel('manufacturer/manufacturer');

        if (null !== $manufacturerId) {
            $manufacturerModel->load($manufacturerId);
        }

        return $manufacturerModel;
    }

    /**
     *
     * @param string $imageUrl
     * @param string $imageDir
     * @param string $imageName
     *
     * @return bool
     */
    protected function _importManufacturerImage($imageUrl, $imageDir, $imageName)
    {
        /* @var $bridgeFileHelper Powerbody_Bridge_Helper_File */
        $bridgeFileHelper = Mage::helper('bridge/file');
        if ($imageUrl && $imageDir && $imageName && !file_exists($imageDir . DS . $imageName)) {
            return $bridgeFileHelper->saveFileToDirectoryFromUrl($imageUrl, $imageDir, $imageName);
        }

        return false;
    }

    /**
     * @param array $itemDataArray
     *
     * @return bool
     */
    protected function _importManufacturerImages($itemDataArray)
    {
        $logoDirPath = Mage::getBaseDir('media') . DS . self::DIR_MEDIA_MANUFACTURER;
        $normalLogoDirPath = $logoDirPath . DS . 'normal';
        $logoUrl = null;
        if (true === isset($itemDataArray['logo_url'])) {
            $logoUrl = $itemDataArray['logo_url'];
        }
        $logo = null;
        if (true === isset($itemDataArray['logo'])) {
            $logo = $itemDataArray['logo'];
        }
        $logoCopyResult = $this->_importManufacturerImage($logoUrl, $logoDirPath, $logo);

        $normalLogoUrl = null;
        if (true === isset($itemDataArray['normal_logo_url'])) {
            $normalLogoUrl = $itemDataArray['normal_logo_url'];
        }
        $logoNormal = null;
        if (true === isset($itemDataArray['logo_normal'])) {
            $logoNormal = $itemDataArray['logo_normal'];
        }
        $normalLogoCopyResult = $this->_importManufacturerImage($normalLogoUrl, $normalLogoDirPath, $logoNormal);

        return $logoCopyResult && $normalLogoCopyResult;
    }

    /**
     * @return Powerbody_Bridge_Model_Service
     */
    protected function _getService()
    {
        return $this->_service;
    }
}
