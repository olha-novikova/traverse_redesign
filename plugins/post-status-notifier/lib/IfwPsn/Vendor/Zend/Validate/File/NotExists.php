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
 * @category  Zend
 * @package   IfwPsn_Vendor_Zend_Validate
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id: NotExists.php 269 2014-04-25 23:29:54Z timoreithde $
 */

/**
 * @see IfwPsn_Vendor_Zend_Validate_File_Exists
 */
require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Validate/File/Exists.php';

/**
 * Validator which checks if the destination file does not exist
 *
 * @category  Zend
 * @package   IfwPsn_Vendor_Zend_Validate
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class IfwPsn_Vendor_Zend_Validate_File_NotExists extends IfwPsn_Vendor_Zend_Validate_File_Exists
{
    /**
     * @const string Error constants
     */
    const DOES_EXIST = 'fileNotExistsDoesExist';

    /**
     * @var array Error message templates
     */
    protected $_messageTemplates = array(
        self::DOES_EXIST => "File '%value%' exists",
    );

    /**
     * Defined by IfwPsn_Vendor_Zend_Validate_Interface
     *
     * Returns true if and only if the file does not exist in the set destinations
     *
     * @param  string  $value Real file to check for
     * @param  array   $file  File data from IfwPsn_Vendor_Zend_File_Transfer
     * @return boolean
     */
    public function isValid($value, $file = null)
    {
        $directories = $this->getDirectory(true);
        if (($file !== null) and (!empty($file['destination']))) {
            $directories[] = $file['destination'];
        } else if (!isset($file['name'])) {
            $file['name'] = $value;
        }

        foreach ($directories as $directory) {
            if (empty($directory)) {
                continue;
            }

            $check = true;
            if (file_exists($directory . DIRECTORY_SEPARATOR . $file['name'])) {
                return $this->_throw($file, self::DOES_EXIST);
            }
        }

        if (!isset($check)) {
            return $this->_throw($file, self::DOES_EXIST);
        }

        return true;
    }
}
