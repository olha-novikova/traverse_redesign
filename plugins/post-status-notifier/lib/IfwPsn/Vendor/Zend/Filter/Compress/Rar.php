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
 * @version    $Id: Rar.php 269 2014-04-25 23:29:54Z timoreithde $
 */

/**
 * @see IfwPsn_Vendor_Zend_Filter_Compress_CompressAbstract
 */
require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Filter/Compress/CompressAbstract.php';

/**
 * Compression adapter for Rar
 *
 * @category   Zend
 * @package    IfwPsn_Vendor_Zend_Filter
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IfwPsn_Vendor_Zend_Filter_Compress_Rar extends IfwPsn_Vendor_Zend_Filter_Compress_CompressAbstract
{
    /**
     * Compression Options
     * array(
     *     'callback' => Callback for compression
     *     'archive'  => Archive to use
     *     'password' => Password to use
     *     'target'   => Target to write the files to
     * )
     *
     * @var array
     */
    protected $_options = array(
        'callback' => null,
        'archive'  => null,
        'password' => null,
        'target'   => '.',
    );

    /**
     * Class constructor
     *
     * @param array $options (Optional) Options to set
     */
    public function __construct($options = null)
    {
        if (!extension_loaded('rar')) {
            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Filter/Exception.php';
            throw new IfwPsn_Vendor_Zend_Filter_Exception('This filter needs the rar extension');
        }
        parent::__construct($options);
    }

    /**
     * Returns the set callback for compression
     *
     * @return string
     */
    public function getCallback()
    {
        return $this->_options['callback'];
    }

    /**
     * Sets the callback to use
     *
     * @param string $callback
     * @return IfwPsn_Vendor_Zend_Filter_Compress_Rar
     */
    public function setCallback($callback)
    {
        if (!is_callable($callback)) {
            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Filter/Exception.php';
            throw new IfwPsn_Vendor_Zend_Filter_Exception('Callback can not be accessed');
        }

        $this->_options['callback'] = $callback;
        return $this;
    }

    /**
     * Returns the set archive
     *
     * @return string
     */
    public function getArchive()
    {
        return $this->_options['archive'];
    }

    /**
     * Sets the archive to use for de-/compression
     *
     * @param string $archive Archive to use
     * @return IfwPsn_Vendor_Zend_Filter_Compress_Rar
     */
    public function setArchive($archive)
    {
        $archive = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $archive);
        $this->_options['archive'] = (string) $archive;

        return $this;
    }

    /**
     * Returns the set password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->_options['password'];
    }

    /**
     * Sets the password to use
     *
     * @param string $password
     * @return IfwPsn_Vendor_Zend_Filter_Compress_Rar
     */
    public function setPassword($password)
    {
        $this->_options['password'] = (string) $password;
        return $this;
    }

    /**
     * Returns the set targetpath
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->_options['target'];
    }

    /**
     * Sets the targetpath to use
     *
     * @param string $target
     * @return IfwPsn_Vendor_Zend_Filter_Compress_Rar
     */
    public function setTarget($target)
    {
        if (!file_exists(dirname($target))) {
            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Filter/Exception.php';
            throw new IfwPsn_Vendor_Zend_Filter_Exception("The directory '$target' does not exist");
        }

        $target = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $target);
        $this->_options['target'] = (string) $target;
        return $this;
    }

    /**
     * Compresses the given content
     *
     * @param  string|array $content
     * @return string
     */
    public function compress($content)
    {
        $callback = $this->getCallback();
        if ($callback === null) {
            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Filter/Exception.php';
            throw new IfwPsn_Vendor_Zend_Filter_Exception('No compression callback available');
        }

        $options = $this->getOptions();
        unset($options['callback']);

        $result = call_user_func($callback, $options, $content);
        if ($result !== true) {
            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Filter/Exception.php';
            throw new IfwPsn_Vendor_Zend_Filter_Exception('Error compressing the RAR Archive');
        }

        return $this->getArchive();
    }

    /**
     * Decompresses the given content
     *
     * @param  string $content
     * @return boolean
     */
    public function decompress($content)
    {
        $archive = $this->getArchive();
        if (file_exists($content)) {
            $archive = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, realpath($content));
        } elseif (empty($archive) || !file_exists($archive)) {
            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Filter/Exception.php';
            throw new IfwPsn_Vendor_Zend_Filter_Exception('RAR Archive not found');
        }

        $password = $this->getPassword();
        if ($password !== null) {
            $archive = rar_open($archive, $password);
        } else {
            $archive = rar_open($archive);
        }

        if (!$archive) {
            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Filter/Exception.php';
            throw new IfwPsn_Vendor_Zend_Filter_Exception("Error opening the RAR Archive");
        }

        $target = $this->getTarget();
        if (!is_dir($target)) {
            $target = dirname($target);
        }

        $filelist = rar_list($archive);
        if (!$filelist) {
            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Filter/Exception.php';
            throw new IfwPsn_Vendor_Zend_Filter_Exception("Error reading the RAR Archive");
        }

        foreach($filelist as $file) {
            $file->extract($target);
        }

        rar_close($archive);
        return true;
    }

    /**
     * Returns the adapter name
     *
     * @return string
     */
    public function toString()
    {
        return 'Rar';
    }
}
