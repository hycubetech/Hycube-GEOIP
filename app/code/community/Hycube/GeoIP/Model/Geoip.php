<?php

/**
 * Hycube
 * GeoIP Extension
 *
 * @category   Hycube
 * @package    Hycube_GeoIP
 * @copyright  Copyright (c) 2015 Hycube (http://www.hycube.com/)
 */
class Hycube_GeoIP_Model_Geoip extends Mage_Core_Model_Abstract
{
    protected $_geoip;

    /**
     * Gets location by ip address
     *
     * @param string $ip
     * @return array
     */
    public function getLocation($ip = null)
    {
        if (is_null($ip)) {
            return $this->getCurrentLocation();
        }

        $this->load($ip);

        return $this;
    }

    /**
     * Return current customer loaction
     *
     * @return mixed
     */
    public function getCurrentLocation()
    {
        $session = Mage::getSingleton('core/session');

        if (!$session->getCustomerLocation() || !$session->getCustomerLocation()->getCode()) {
            $ip = Mage::helper('hycube_geoip')->getCustomerIp();
            $this->load($ip);
            $session->setCustomerLocation($this);
        }

        return $session->getCustomerLocation();
    }

    /**
     * Loads location data by ip and puts it in object
     *
     * @param string $ip
     * @param string|bool $field
     * @return Hycube_GeoIP_Model_Geoip
     */
    public function load($ip, $field = null)
    {
        if (!Mage::helper('hycube_geoip')->checkDatabaseFile()) {
            return $this;
        }

        $config = array(
            'is_city_db_type' => Mage::helper('hycube_geoip')->isCityDbType(),
            'db_path' => Mage::helper('hycube_geoip')->getDatabasePath(),
        );

        $data = $this->getGeoIpLocation($ip, $config);

        if (isset($data['code'])) {
            $data['flag'] = Mage::helper('hycube_geoip')->getFlagPath($data['code']);
        }

        $this->setData($data);

        return $this;
    }

    /**
     * Gets customer location data by ip
     *
     * @static
     * @param string $ip
     * @param array $config
     * @return array
     */
    public static function getGeoIpLocation($ip, $config)
    {
        include_once Mage::getBaseDir() . DS . 'lib' . DS . 'Hycube' . DS . 'GeoIP' . DS . 'geoIp.php';
        $geoIp = new \GeoIP($config['db_path'], \GeoIP::GEOIP_STANDARD);
        $data = array('ip' => $ip);

        if ($config['is_city_db_type']) {
            $record = $geoIp->getRecordByAddress($ip);
//            $record = geoip_record_by_addr($geoip, $ip);

            if ($record) {
                $data['code'] = $record->countryCode;
                $data['country'] = $record->countryName;
                $data['region'] = (isset($geoIpRegionName[$record->countryCode][$record->region]) ? $geoIpRegionName[$record->countryCode][$record->region] : $record->region);
                $data['city'] = $record->city;
                $data['postal_code'] = $record->postalCode;
            }
        } else {
            $data['code'] = $geoIp->getCountryCodeByAddress($ip);
//            $data['code'] = geoip_country_code_by_addr($geoip, $ip);
            $data['country'] = $geoIp->getCountryNameByAddress($ip);
//            $data['country'] = geoip_country_name_by_addr($geoip, $ip);
        }

        // @see \GeoIP::__destruct() method
//        geoip_close($geoip);

        return $data;
    }

    /**
     * Change current customer location
     *
     * @return bool
     */
    public function changeCurrentLocation($countryCode)
    {
        $session = Mage::getSingleton('core/session');
        if ($location = $session->getCustomerLocation()) {
            $location->setCode($countryCode);
            $session->setCustomerLocation($location);
        }

        return true;
    }

    /**
     * Downloads file from remote server
     *
     * @param strung $source
     * @param string $destination
     * @return bool
     */
    public function downloadFile($source, $destination)
    {
        $errors = array();

        $sourceFile = curl_init($source);
        $newFile = @fopen($destination, "wb");

        if (!$sourceFile) {
            $errors[] = Mage::helper('hycube_geoip')->__('DataBase source is temporary unavailable');
        }

        if (!$newFile) {
            $errors[] = sprintf(Mage::helper('hycube_geoip')->__("Can't create new file %s. Check if contained folder has write permissions."), $destination);
        }

        if (!empty($errors)) {
            return $errors;
        }

        curl_setopt($sourceFile, CURLOPT_FILE, $newFile);
        $data = curl_exec($sourceFile);

        curl_close($sourceFile);
        fclose($newFile);

        return $errors;
    }

    /**
     * Unpacks .gz archive
     *
     * @param string $source
     * @param string $destination
     * @return bool
     */
    public function uncompressFile($source, $destination)
    {
        $sourceFile = @gzopen($source, "rb");
        $newFile = @fopen($destination, "wb");

        if (!$sourceFile || !$newFile) {
            return false;
        }

        while ($string = gzread($sourceFile, 4096)) {
            fwrite($newFile, $string, strlen($string));
        }

        gzclose($sourceFile);
        fclose($newFile);

        return true;
    }
}
