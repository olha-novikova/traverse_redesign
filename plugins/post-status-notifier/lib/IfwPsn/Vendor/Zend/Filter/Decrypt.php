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
 * @version    $Id: Decrypt.php 269 2014-04-25 23:29:54Z timoreithde $
 */

/**
 * @see IfwPsn_Vendor_Zend_Filter_Encrypt
 */
require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Filter/Encrypt.php';

/**
 * Decrypts a given string
 *
 * @category   Zend
 * @package    IfwPsn_Vendor_Zend_Filter
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IfwPsn_Vendor_Zend_Filter_Decrypt extends IfwPsn_Vendor_Zend_Filter_Encrypt
{
    /**
     * Defined by IfwPsn_Vendor_Zend_Filter_Interface
     *
     * Decrypts the content $value with the defined settings
     *
     * @param  string $value Content to decrypt
     * @return string The decrypted content
     */
    public function filter($value)
    {
        return $this->_adapter->decrypt($value);
    }
}
