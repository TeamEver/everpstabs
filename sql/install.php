<?php
/**
 * 2019-2022 Team Ever
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 *  @author    Team Ever <https://www.team-ever.com/>
 *  @copyright 2019-2022 Team Ever
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

$sql = array();
/* Create Tables in Database */
$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'everpstabs` (
         `id_everpstabs` int(10) unsigned NOT NULL auto_increment,
         `id_product` int(10) unsigned NOT NULL,
         `id_shop` int(10) unsigned DEFAULT 0,
         PRIMARY KEY (`id_everpstabs`))
         ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'everpstabs_lang` (
         `id_everpstabs` int(10) unsigned NOT NULL,
         `id_lang` int(10) unsigned NOT NULL,
         `title` varchar(255) DEFAULT NULL,
         `content` text DEFAULT NULL,
         PRIMARY KEY (`id_everpstabs`, `id_lang`))
         ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
