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
 * @package    IfwPsn_Vendor_Zend_Filter
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Encrypt.php 269 2014-04-25 23:29:54Z timoreithde $
 */

/**
 * @see IfwPsn_Vendor_Zend_Filter_Interface
 */
require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Filter/Interface.php';

/**
 * @see IfwPsn_Vendor_Zend_Loader
 */
require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Zend/Loader.php';

/**
 * Encrypts a given string
 *
 * @category   Zend
 * @package    IfwPsn_Vendor_Zend_Filter
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IfwPsn_Vendor_Zend_Filter_Encrypt implements IfwPsn_Vendor_Zend_Filter_Interface
{
    /**
     * Encryption adapter
     */
    protected $_adapter;

    /**
     * Class constructor
     *
     * @param string|array $options (Optional) Options to set, if null mcrypt is used
     */
    public function __construct($options = null)
    {
        if ($options instanceof IfwPsn_Vendor_Zend_Config) {
            $options = $options->toArray();
        }

        $this->setAdapter($options);
    }

    /**
     * Returns the name of the set adapter
     *
     * @return string
     */
    public function getAdapter()
    {
        return $this->_adapter->toString();
    }

    /**
     * Sets new encryption options
     *
     * @param  string|array $options (Optional) Encryption options
     * @return IfwPsn_Vendor_Zend_Filter_Encrypt
     */
    public function setAdapter($options = null)
    {
        if (is_string($options)) {
            $adapter = $options;
        } else if (isset($options['adapter'])) {
            $adapter = $options['adapter'];
            unset($options['adapter']);
        } else {
            $adapter = 'Mcrypt';
        }

        if (!is_array($options)) {
            $options = array();
        }

        if (IfwPsn_Zend_Loader::isReadable('IfwPsn/Vendor/Zend/Filter/Encrypt/' . ucfirst($adapter). '.php')) {
            $adapter = 'IfwPsn_Vendor_Zend_Filter_Encrypt_' . ucfirst($adapter);
        }

        if (!class_exists($adapter)) {
            IfwPsn_Zend_Loader::loadClass($adapter);
        }

        $this->_adapter = new $adapter($options);
        if (!$this->_adapter instanceof IfwPsn_Vendor_Zend_Filter_Encrypt_Interface) {
            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Filter/Exception.php';
            throw new IfwPsn_Vendor_Zend_Filter_Exception("Encoding adapter '" . $adapter . "' does not implement IfwPsn_Vendor_Zend_Filter_Encrypt_Interface");
        }

        return $this;
    }

    /**
     * Calls adapter methods
     *
     * @param string       $method  Method to call
     * @param string|array $options Options for this method
     */
    public function __call($method, $options)
    {
        $part = substr($method, 0, 3);
        if ((($part != 'get') and ($part != 'set')) or !method_exists($this->_adapter, $method)) {
            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Filter/Exception.php';
            throw new IfwPsn_Vendor_Zend_Filter_Exception("Unknown method '{$method}'");
        }

        return call_user_func_array(array($this->_adapter, $method), $options);
    }

    /**
     * Defined by IfwPsn_Vendor_Zend_Filter_Interface
     *
     * Encrypts the content $value with the defined settings
     *
     * @param  string $value Content to encrypt
     * @return string The encrypted content
     */
    public function filter($value)
    {
        return $this->_adapter->encrypt($value);
    }
}
