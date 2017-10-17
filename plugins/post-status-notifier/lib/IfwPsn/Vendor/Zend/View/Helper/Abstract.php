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
 * @package    IfwPsn_Vendor_Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Abstract.php 269 2014-04-25 23:29:54Z timoreithde $
 */

/**
 * @see IfwPsn_Vendor_Zend_View_Helper_Interface
 */
require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/View/Helper/Interface.php';

/**
 * @category   Zend
 * @package    IfwPsn_Vendor_Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class IfwPsn_Vendor_Zend_View_Helper_Abstract implements IfwPsn_Vendor_Zend_View_Helper_Interface
{
    /**
     * View object
     *
     * @var IfwPsn_Vendor_Zend_View_Interface
     */
    public $view = null;

    /**
     * Set the View object
     *
     * @param  IfwPsn_Vendor_Zend_View_Interface $view
     * @return IfwPsn_Vendor_Zend_View_Helper_Abstract
     */
    public function setView(IfwPsn_Vendor_Zend_View_Interface $view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * Strategy pattern: currently unutilized
     *
     * @return void
     */
    public function direct()
    {
    }
}
