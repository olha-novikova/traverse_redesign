<?php
/**
 *
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) 2014 ifeelweb.de
 * @version   $Id: MailQueueLog.php 333 2014-11-08 00:16:07Z timoreithde $
 * @package
 */
class Psn_Module_DeferredSending_Model_MailQueueLog extends IfwPsn_Wp_ORM_Model
{
    /**
     * @var string
     */
    public static $_table = 'psn_mail_queue_log';



    /**
     * Installs the table
     *
     * @param bool $networkwide
     */
    public function createTable($networkwide = false)
    {
        global $wpdb;

        $query = '
            CREATE TABLE IF NOT EXISTS `%s` (
              `id` int(11) NOT NULL auto_increment,
              `to` text collate utf8_unicode_ci NOT NULL,
              `subject` text collate utf8_unicode_ci NOT NULL,
              `message` text collate utf8_unicode_ci NOT NULL,
              `headers` text collate utf8_unicode_ci,
              `attachments` text collate utf8_unicode_ci,
              `altbody` text COLLATE utf8_unicode_ci,
              `html` tinyint(1) NOT NULL DEFAULT "0",
              `added` datetime NOT NULL,
              `scheduled` datetime NOT NULL,
              `sent` datetime DEFAULT NULL,
              `tries` tinyint(2) NOT NULL DEFAULT "0",
              `options` text COLLATE utf8_unicode_ci,
              PRIMARY KEY  (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT="Plugin: Post Status Notifier";
        ';

        if (!$networkwide) {
            // single blog installation
            $wpdb->query(sprintf($query, $wpdb->prefix . self::$_table));
        } else {
            // multisite installation
            $currentBlogId = IfwPsn_Wp_Proxy_Blog::getBlogId();
            foreach (IfwPsn_Wp_Proxy_Blog::getMultisiteBlogIds() as $blogId) {
                IfwPsn_Wp_Proxy_Blog::switchToBlog($blogId);
                $wpdb->query(sprintf($query, $wpdb->prefix . self::$_table));
            }
            IfwPsn_Wp_Proxy_Blog::switchToBlog($currentBlogId);
        }
    }

    /**
     * Uninstalls the table
     *
     * @param bool $networkwide
     */
    public function dropTable($networkwide = false)
    {
        global $wpdb;

        if (!$networkwide) {
            // single blog installation
            $wpdb->query('DROP TABLE IF EXISTS `'. $wpdb->prefix . self::$_table);

        } else {
            // multisite installation
            $currentBlogId = IfwPsn_Wp_Proxy_Blog::getBlogId();
            foreach (IfwPsn_Wp_Proxy_Blog::getMultisiteBlogIds() as $blogId) {
                IfwPsn_Wp_Proxy_Blog::switchToBlog($blogId);
                $wpdb->query('DROP TABLE IF EXISTS `'. $wpdb->prefix . self::$_table);
            }
            IfwPsn_Wp_Proxy_Blog::switchToBlog($currentBlogId);
        }
    }

}

