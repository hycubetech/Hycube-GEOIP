<?php
/**
 * Copyright © 2017 Hycube. All rights reserved.
 * See LICENSE.txt for license details.
 */

/**
 * Class geoIpRecord
 * 
 * Replacement of the geoiprecord class
 */
class geoIpRecord
{
    public $countryCode;
    public $countryCode3;
    public $countryName;
    public $region;
    public $city;
    public $postalCode;
    public $latitude;
    public $longitude;
    public $areaCode;
    public $dmaCode;   # metro and dma code are the same. use metro_code
    public $metroCode;
}
