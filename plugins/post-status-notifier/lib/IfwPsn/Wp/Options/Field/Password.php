<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 *
 * Options field password
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: Password.php 233 2014-03-17 23:46:37Z timoreithde $
 */
require_once dirname(__FILE__) . '/../Field.php';

class IfwPsn_Wp_Options_Field_Password extends IfwPsn_Wp_Options_Field
{
    public function render(array $params)
    {
        /**
         * @var IfwPsn_Wp_Options
         */
        $options = $params[0];

        $id = $options->getOptionRealId($this->_id);
        $name = $options->getPageId() . '['. $id .']';

        $html = '<input type="password" autocomplete="off" id="'. $id .'" name="'. $name .'" value="'. $options->getOption($this->_id) .'" />';
        if (!empty($this->_description)) {
            $html .= '<br><p class="description"> '  . $this->_description . '</p>';
        }
        echo $html;
    }
}
