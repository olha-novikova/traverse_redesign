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
 * @version    $Id: Bz2.php 269 2014-04-25 23:29:54Z timoreithde $
 */

/**
 * @see IfwPsn_Vendor_Zend_Filter_Compress_CompressAbstract
 */
require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Filter/Compress/CompressAbstract.php';

/**
 * Compression adapter for Bz2
 *
 * @category   Zend
 * @package    IfwPsn_Vendor_Zend_Filter
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IfwPsn_Vendor_Zend_Filter_Compress_Bz2 extends IfwPsn_Vendor_Zend_Filter_Compress_CompressAbstract
{
    /**
     * Compression Options
     * array(
     *     'blocksize' => Blocksize to use from 0-9
     *     'archive'   => Archive to use
     * )
     *
     * @var array
     */
    protected $_options = array(
        'blocksize' => 4,
        'archive'   => null,
    );

    /**
     * Class constructor
     *
     * @param array|IfwPsn_Vendor_Zend_Config $options (Optional) Options to set
     */
    public function __construct($options = null)
    {
        if (!extension_loaded('bz2')) {
            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Filter/Exception.php';
            throw new IfwPsn_Vendor_Zend_Filter_Exception('This filter needs the bz2 extension');
        }
        parent::__construct($options);
    }

    /**
     * Returns the set blocksize
     *
     * @return integer
     */
    public function getBlocksize()
    {
        return $this->_options['blocksize'];
    }

    /**
     * Sets a new blocksize
     *
     * @param integer $level
     * @return IfwPsn_Vendor_Zend_Filter_Compress_Bz2
     */
    public function setBlocksize($blocksize)
    {
        if (($blocksize < 0) || ($blocksize > 9)) {
            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Filter/Exception.php';
            throw new IfwPsn_Vendor_Zend_Filter_Exception('Blocksize must be between 0 and 9');
        }

        $this->_options['blocksize'] = (int) $blocksize;
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
     * @return IfwPsn_Vendor_Zend_Filter_Compress_Bz2
     */
    public function setArchive($archive)
    {
        $this->_options['archive'] = (string) $archive;
        return $this;
    }

    /**
     * Compresses the given content
     *
     * @param  string $content
     * @return string
     */
    public function compress($content)
    {
        $archive = $this->getArchive();
        if (!empty($archive)) {
            $file = bzopen($archive, 'w');
            if (!$file) {
                require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Filter/Exception.php';
                throw new IfwPsn_Vendor_Zend_Filter_Exception("Error opening the archive '" . $archive . "'");
            }

            bzwrite($file, $content);
            bzclose($file);
            $compressed = true;
        } else {
            $compressed = bzcompress($content, $this->getBlocksize());
        }

        if (is_int($compressed)) {
            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Filter/Exception.php';
            throw new IfwPsn_Vendor_Zend_Filter_Exception('Error during compression');
        }

        return $compressed;
    }

    /**
     * Decompresses the given content
     *
     * @param  string $content
     * @return string
     */
    public function decompress($content)
    {
        $archive = $this->getArchive();
        if (@file_exists($content)) {
            $archive = $content;
        }

        if (@file_exists($archive)) {
            $file = bzopen($archive, 'r');
            if (!$file) {
                require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Filter/Exception.php';
                throw new IfwPsn_Vendor_Zend_Filter_Exception("Error opening the archive '" . $content . "'");
            }

            $compressed = bzread($file);
            bzclose($file);
        } else {
            $compressed = bzdecompress($content);
        }

        if (is_int($compressed)) {
            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Filter/Exception.php';
            throw new IfwPsn_Vendor_Zend_Filter_Exception('Error during decompression');
        }

        return $compressed;
    }

    /**
     * Returns the adapter name
     *
     * @return string
     */
    public function toString()
    {
        return 'Bz2';
    }
}
