<?php

/**
 * Class Powerbody_Bridge_Helper_File
 */
class Powerbody_Bridge_Helper_File extends Mage_Core_Helper_Abstract
{
    /**
     * @param string $imageUrl
     *
     * @return null|string
     */
    public function getImagePathFromUrlByCURL($imageUrl) 
    {
        $curl = curl_init($imageUrl);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_BINARYTRANSFER, 1);
        $rawData = curl_exec($curl);
        curl_close($curl);
        $path = null;
        if ($rawData) {
            if (!file_exists(Mage::getBaseDir('media') . DS . 'import')) {
                mkdir(Mage::getBaseDir('media') . DS . 'import', 0777, true);
            }
            $path = Mage::getBaseDir('media') . DS . 'import' . DS . md5($imageUrl) . '.jpg';
            if (file_exists($path)) {
                unlink($path);
            }
            $fp = fopen($path, 'x');
            fwrite($fp, $rawData);
            fclose($fp);
        }

        return $path;
    }
    
    /**
     * @param string $filePath
     *
     * @return bool
     */
    public function removeFile($filePath) 
    {
        if (file_exists($filePath) && !is_dir($filePath)) {
            return unlink($filePath);
        }

        return false;
    }
    
    /**
     * @param string $imageUrl
     * @param string $destinationDir
     * @param string $fileName
     *
     * @return bool
     */
    public function saveFileToDirectoryFromUrl($imageUrl, $destinationDir, $fileName)
    {
        $baseFilePath = $this->getImagePathFromUrlByCURL($imageUrl);
        if (file_exists($baseFilePath)) {
            if (!file_exists($destinationDir)) {
                mkdir($destinationDir, 0777, true);
            }
            $newFilePath = $destinationDir . DS . $fileName;
            $copyResult = copy($baseFilePath, $destinationDir . DS . $fileName);
            if ($copyResult && file_exists($newFilePath)) {
                unlink($baseFilePath);
                return true;
            }
        }

        return false;
    }
}
