<?php

/**
 * Hycube
 * GeoIP Extension
 *
 * @category   Hycube
 * @package    Hycube_GeoIP
 * @copyright  Copyright (c) 2015 Hycube (http://www.hycube.com/)
 */
class Hycube_GeoIP_Block_Adminhtml_Notifications extends Mage_Adminhtml_Block_Template
{
    /**
     * Get Notification message
     *
     * @return string
     */
    public function getMessage()
    {
        return Mage::helper('hycube_geoip')->getMissingDbWarning();
    }

    /**
     * ACL validation before html generation
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (Mage::getSingleton('admin/session')->isAllowed('system/config/hycube_geoip/geoip')) {
            return parent::_toHtml();
        }

        return '';
    }
}
