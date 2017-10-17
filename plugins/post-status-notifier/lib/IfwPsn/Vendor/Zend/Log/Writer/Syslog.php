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
 * @package    IfwPsn_Vendor_Zend_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Syslog.php 269 2014-04-25 23:29:54Z timoreithde $
 */

/** IfwPsn_Vendor_Zend_Log */
require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Log.php';

/** IfwPsn_Vendor_Zend_Log_Writer_Abstract */
require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Log/Writer/Abstract.php';

/**
 * Writes log messages to syslog
 *
 * @category   Zend
 * @package    IfwPsn_Vendor_Zend_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IfwPsn_Vendor_Zend_Log_Writer_Syslog extends IfwPsn_Vendor_Zend_Log_Writer_Abstract
{
    /**
     * Maps IfwPsn_Vendor_Zend_Log priorities to PHP's syslog priorities
     *
     * @var array
     */
    protected $_priorities = array(
        IfwPsn_Vendor_Zend_Log::EMERG  => LOG_EMERG,
        IfwPsn_Vendor_Zend_Log::ALERT  => LOG_ALERT,
        IfwPsn_Vendor_Zend_Log::CRIT   => LOG_CRIT,
        IfwPsn_Vendor_Zend_Log::ERR    => LOG_ERR,
        IfwPsn_Vendor_Zend_Log::WARN   => LOG_WARNING,
        IfwPsn_Vendor_Zend_Log::NOTICE => LOG_NOTICE,
        IfwPsn_Vendor_Zend_Log::INFO   => LOG_INFO,
        IfwPsn_Vendor_Zend_Log::DEBUG  => LOG_DEBUG,
    );

    /**
     * The default log priority - for unmapped custom priorities
     *
     * @var string
     */
    protected $_defaultPriority = LOG_NOTICE;

    /**
     * Last application name set by a syslog-writer instance
     *
     * @var string
     */
    protected static $_lastApplication;

    /**
     * Last facility name set by a syslog-writer instance
     *
     * @var string
     */
    protected static $_lastFacility;

    /**
     * Application name used by this syslog-writer instance
     *
     * @var string
     */
    protected $_application = 'IfwPsn_Vendor_Zend_Log';

    /**
     * Facility used by this syslog-writer instance
     *
     * @var int
     */
    protected $_facility = LOG_USER;

    /**
     * Types of program available to logging of message
     *
     * @var array
     */
    protected $_validFacilities = array();

    /**
     * Class constructor
     *
     * @param  array $params Array of options; may include "application" and "facility" keys
     * @return void
     */
    public function __construct(array $params = array())
    {
        if (isset($params['application'])) {
            $this->_application = $params['application'];
        }

        $runInitializeSyslog = true;
        if (isset($params['facility'])) {
            $this->setFacility($params['facility']);
            $runInitializeSyslog = false;
        }

        if ($runInitializeSyslog) {
            $this->_initializeSyslog();
        }
    }

    /**
     * Create a new instance of IfwPsn_Vendor_Zend_Log_Writer_Syslog
     *
     * @param  array|IfwPsn_Vendor_Zend_Config $config
     * @return IfwPsn_Vendor_Zend_Log_Writer_Syslog
     */
    static public function factory($config)
    {
        return new self(self::_parseConfig($config));
    }

    /**
     * Initialize values facilities
     *
     * @return void
     */
    protected function _initializeValidFacilities()
    {
        $constants = array(
            'LOG_AUTH',
            'LOG_AUTHPRIV',
            'LOG_CRON',
            'LOG_DAEMON',
            'LOG_KERN',
            'LOG_LOCAL0',
            'LOG_LOCAL1',
            'LOG_LOCAL2',
            'LOG_LOCAL3',
            'LOG_LOCAL4',
            'LOG_LOCAL5',
            'LOG_LOCAL6',
            'LOG_LOCAL7',
            'LOG_LPR',
            'LOG_MAIL',
            'LOG_NEWS',
            'LOG_SYSLOG',
            'LOG_USER',
            'LOG_UUCP'
        );

        foreach ($constants as $constant) {
            if (defined($constant)) {
                $this->_validFacilities[] = constant($constant);
            }
        }
    }

    /**
     * Initialize syslog / set application name and facility
     *
     * @return void
     */
    protected function _initializeSyslog()
    {
        self::$_lastApplication = $this->_application;
        self::$_lastFacility    = $this->_facility;
        openlog($this->_application, LOG_PID, $this->_facility);
    }

    /**
     * Set syslog facility
     *
     * @param  int $facility Syslog facility
     * @return IfwPsn_Vendor_Zend_Log_Writer_Syslog
     * @throws IfwPsn_Vendor_Zend_Log_Exception for invalid log facility
     */
    public function setFacility($facility)
    {
        if ($this->_facility === $facility) {
            return $this;
        }

        if (!count($this->_validFacilities)) {
            $this->_initializeValidFacilities();
        }

        if (!in_array($facility, $this->_validFacilities)) {
            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Log/Exception.php';
            throw new IfwPsn_Vendor_Zend_Log_Exception('Invalid log facility provided; please see http://php.net/openlog for a list of valid facility values');
        }

        if ('WIN' == strtoupper(substr(PHP_OS, 0, 3))
            && ($facility !== LOG_USER)
        ) {
            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Log/Exception.php';
            throw new IfwPsn_Vendor_Zend_Log_Exception('Only LOG_USER is a valid log facility on Windows');
        }

        $this->_facility = $facility;
        $this->_initializeSyslog();
        return $this;
    }

    /**
     * Set application name
     *
     * @param  string $application Application name
     * @return IfwPsn_Vendor_Zend_Log_Writer_Syslog
     */
    public function setApplicationName($application)
    {
        if ($this->_application === $application) {
            return $this;
        }
        $this->_application = $application;
        $this->_initializeSyslog();
        return $this;
    }

    /**
     * Close syslog.
     *
     * @return void
     */
    public function shutdown()
    {
        closelog();
    }

    /**
     * Write a message to syslog.
     *
     * @param  array $event event data
     * @return void
     */
    protected function _write($event)
    {
        if (array_key_exists($event['priority'], $this->_priorities)) {
            $priority = $this->_priorities[$event['priority']];
        } else {
            $priority = $this->_defaultPriority;
        }

        if ($this->_application !== self::$_lastApplication
            || $this->_facility !== self::$_lastFacility)
        {
            $this->_initializeSyslog();
        }

        $message = $event['message'];
        if ($this->_formatter instanceof IfwPsn_Vendor_Zend_Log_Formatter_Interface) {
            $message = $this->_formatter->format($event);
        }

        syslog($priority, $message);
    }
}
