<?php

/**
 * Class Powerbody_Bridge_Model_Imported_Category
 */
class Powerbody_Bridge_Model_Imported_Category extends Mage_Core_Model_Abstract
{
    /**
     * construct
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('bridge/imported_category');
    }

    /**
     * @return string
     */
    public function getInternalPath()
    {
        $externalPath = $this->getData('path');
        $externalPath = explode('/', $externalPath);

        return $this->_generateInternalPath($externalPath);
    }

    /**
     * @param array $externalPath
     *
     * @return string
     */
    protected function _generateInternalPath(array $externalPath)
    {
        $connection = $this->getResource()->getReadConnection();
        $statement = $connection
            ->select()
            ->from($this->_getResource()->getTable('bridge/imported_category'))
            ->where('base_category_id in (?)', $externalPath)
            ->query();

        $result = $statement->fetchAll();
        $result = array_column($result, 'client_category_id');
        $result = array_filter($result, function ($value) { return !is_null($value); });

        return implode('/', $result);
    }

    /**
     * before save
     */
    protected function _beforeSave()
    {
        $this->setData('updated_date', Mage::getSingleton('core/date')->gmtDate());

        return parent::_beforeSave();
    }
}
