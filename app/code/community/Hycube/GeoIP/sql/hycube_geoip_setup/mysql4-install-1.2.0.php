<?php
/**
 * Hycube
 * GeoIP Extension
 *
 * @category   Hycube
 * @package    Hycube_GeoIP
 * @copyright  Copyright (c) 2015 Hycube (http://www.hycube.com/)
 */

$installer = $this;

$pathLike = 'hycube_customers/geoip/%';
$configCollection = Mage::getModel('core/config_data')->getCollection();
$configCollection->getSelect()->where('path like ?', $pathLike);

foreach ($configCollection as $conf) {
    $path = $conf->getPath();
    $path = str_replace('hycube_customers', 'hycube_geoip', $path);
    $conf->setPath($path)->save();
}

$installer->endSetup();