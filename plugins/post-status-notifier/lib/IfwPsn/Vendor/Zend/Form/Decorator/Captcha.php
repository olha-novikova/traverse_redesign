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
 * @package    IfwPsn_Vendor_Zend_Form
 * @subpackage Decorator
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** @see IfwPsn_Vendor_Zend_Form_Decorator_Abstract */
require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Form/Decorator/Abstract.php';

/**
 * Captcha generic decorator
 *
 * Adds captcha adapter output
 *
 * @category   Zend
 * @package    IfwPsn_Vendor_Zend_Form
 * @subpackage Decorator
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Captcha.php 269 2014-04-25 23:29:54Z timoreithde $
 */
class IfwPsn_Vendor_Zend_Form_Decorator_Captcha extends IfwPsn_Vendor_Zend_Form_Decorator_Abstract
{
    /**
     * Render captcha
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $element = $this->getElement();
        if (!method_exists($element, 'getCaptcha')) {
            return $content;
        }

        $view    = $element->getView();
        if (null === $view) {
            return $content;
        }

        $placement = $this->getPlacement();
        $separator = $this->getSeparator();

        $captcha = $element->getCaptcha();
        $markup  = $captcha->render($view, $element);
        switch ($placement) {
            case 'PREPEND':
                $content = $markup . $separator .  $content;
                break;
            case 'APPEND':
            default:
                $content = $content . $separator . $markup;
        }
        return $content;
    }
}
