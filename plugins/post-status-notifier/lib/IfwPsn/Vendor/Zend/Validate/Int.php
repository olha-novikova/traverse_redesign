<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    IfwPsn_Vendor_Zend_Validate
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Int.php 269 2014-04-25 23:29:54Z timoreithde $
 */

/**
 * @see IfwPsn_Vendor_Zend_Validate_Abstract
 */
require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Validate/Abstract.php';

/**
 * @see IfwPsn_Vendor_Zend_Locale_Format
 */
require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Locale/Format.php';

/**
 * @category   Zend
 * @package    IfwPsn_Vendor_Zend_Validate
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IfwPsn_Vendor_Zend_Validate_Int extends IfwPsn_Vendor_Zend_Validate_Abstract
{
    const INVALID = 'intInvalid';
    const NOT_INT = 'notInt';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID => "Invalid type given. String or integer expected",
        self::NOT_INT => "'%value%' does not appear to be an integer",
    );

    protected $_locale;

    /**
     * Constructor for the integer validator
     *
     * @param string|IfwPsn_Vendor_Zend_Config|IfwPsn_Vendor_Zend_Locale $locale
     */
    public function __construct($locale = null)
    {
        if ($locale instanceof IfwPsn_Vendor_Zend_Config) {
            $locale = $locale->toArray();
        }

        if (is_array($locale)) {
            if (array_key_exists('locale', $locale)) {
                $locale = $locale['locale'];
            } else {
                $locale = null;
            }
        }

        if (empty($locale)) {
            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Registry.php';
            if (IfwPsn_Vendor_Zend_Registry::isRegistered('IfwPsn_Vendor_Zend_Locale')) {
                $locale = IfwPsn_Vendor_Zend_Registry::get('IfwPsn_Vendor_Zend_Locale');
            }
        }

        if ($locale !== null) {
            $this->setLocale($locale);
        }
    }

    /**
     * Returns the set locale
     */
    public function getLocale()
    {
        return $this->_locale;
    }

    /**
     * Sets the locale to use
     *
     * @param string|IfwPsn_Vendor_Zend_Locale $locale
     */
    public function setLocale($locale = null)
    {
        require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Locale.php';
        $this->_locale = IfwPsn_Vendor_Zend_Locale::findLocale($locale);
        return $this;
    }

    /**
     * Defined by IfwPsn_Vendor_Zend_Validate_Interface
     *
     * Returns true if and only if $value is a valid integer
     *
     * @param  string|integer $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_string($value) && !is_int($value) && !is_float($value)) {
            $this->_error(self::INVALID);
            return false;
        }

        if (is_int($value)) {
            return true;
        }

        $this->_setValue($value);
        if ($this->_locale === null) {
            $locale        = localeconv();
            $valueFiltered = str_replace($locale['decimal_point'], '.', $value);
            $valueFiltered = str_replace($locale['thousands_sep'], '', $valueFiltered);

            if (strval(intval($valueFiltered)) != $valueFiltered) {
                $this->_error(self::NOT_INT);
                return false;
            }

        } else {
            try {
                if (!IfwPsn_Vendor_Zend_Locale_Format::isInteger($value, array('locale' => $this->_locale))) {
                    $this->_error(self::NOT_INT);
                    return false;
                }
            } catch (IfwPsn_Vendor_Zend_Locale_Exception $e) {
                $this->_error(self::NOT_INT);
                return false;
            }
        }

        return true;
    }
}
