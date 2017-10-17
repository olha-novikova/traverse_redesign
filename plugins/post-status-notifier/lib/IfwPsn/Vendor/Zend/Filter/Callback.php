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
 * @version    $Id: Callback.php 269 2014-04-25 23:29:54Z timoreithde $
 */

/**
 * @see IfwPsn_Vendor_Zend_Filter_Interface
 */
require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Filter/Interface.php';

/**
 * @category   Zend
 * @package    IfwPsn_Vendor_Zend_Filter
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IfwPsn_Vendor_Zend_Filter_Callback implements IfwPsn_Vendor_Zend_Filter_Interface
{
    /**
     * Callback in a call_user_func format
     *
     * @var string|array
     */
    protected $_callback = null;

    /**
     * Default options to set for the filter
     *
     * @var mixed
     */
    protected $_options = null;

    /**
     * Constructor
     *
     * @param string|array $callback Callback in a call_user_func format
     * @param mixed        $options  (Optional) Default options for this filter
     */
    public function __construct($options)
    {
        if ($options instanceof IfwPsn_Vendor_Zend_Config) {
            $options = $options->toArray();
        } else if (!is_array($options) || !array_key_exists('callback', $options)) {
            $options          = func_get_args();
            $temp['callback'] = array_shift($options);
            if (!empty($options)) {
                $temp['options'] = array_shift($options);
            }

            $options = $temp;
        }

        if (!array_key_exists('callback', $options)) {
            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Filter/Exception.php';
            throw new IfwPsn_Vendor_Zend_Filter_Exception('Missing callback to use');
        }

        $this->setCallback($options['callback']);
        if (array_key_exists('options', $options)) {
            $this->setOptions($options['options']);
        }
    }

    /**
     * Returns the set callback
     *
     * @return string|array Set callback
     */
    public function getCallback()
    {
        return $this->_callback;
    }

    /**
     * Sets a new callback for this filter
     *
     * @param unknown_type $callback
     * @return unknown
     */
    public function setCallback($callback, $options = null)
    {
        if (!is_callable($callback)) {
            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Filter/Exception.php';
            throw new IfwPsn_Vendor_Zend_Filter_Exception('Callback can not be accessed');
        }

        $this->_callback = $callback;
        $this->setOptions($options);
        return $this;
    }

    /**
     * Returns the set default options
     *
     * @return mixed
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Sets new default options to the callback filter
     *
     * @param mixed $options Default options to set
     * @return IfwPsn_Vendor_Zend_Filter_Callback
     */
    public function setOptions($options)
    {
        $this->_options = $options;
        return $this;
    }

    /**
     * Calls the filter per callback
     *
     * @param mixed $value Options for the set callback
     * @return mixed       Result from the filter which was callbacked
     */
    public function filter($value)
    {
        $options = array();

        if ($this->_options !== null) {
            if (!is_array($this->_options)) {
                $options = array($this->_options);
            } else {
                $options = $this->_options;
            }
        }

        array_unshift($options, $value);

        return call_user_func_array($this->_callback, $options);
    }
}
