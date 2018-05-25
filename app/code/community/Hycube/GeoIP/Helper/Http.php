<?php

/**
 * Hycube
 * GeoIP Extension
 *
 * @category   Hycube
 * @package    Hycube_GeoIP
 * @copyright  Copyright (c) 2015 Hycube (http://www.hycube.com/)
 */
class Hycube_GeoIP_Helper_Http extends Mage_Core_Helper_Http
{
    const ICONV_CHARSET = 'UTF-8';

    /**
     * Gets http user agent
     *
     * @param bool $clean
     * @return string
     */
    public function getHttpUserAgent($clean = true)
    {
        return $this->_getHttpCleanValue('HTTP_USER_AGENT', $clean);
    }

    /**
     * Returns clean http value
     *
     * @param string $var
     * @param bool $clean
     * @return bool
     */
    protected function _getHttpCleanValue($var, $clean = true)
    {
        $value = $this->_getRequest()->getServer($var, '');
        if ($clean) {
            $value = $this->cleanString($value);
        }

        return $value;
    }

    /**
     * checks string
     *
     * @param string $string
     * @return bool
     */
    public function cleanString($string)
    {
        return '"libiconv"' == ICONV_IMPL ? iconv(self::ICONV_CHARSET, self::ICONV_CHARSET . '//IGNORE', $string) : $string;
    }
}
