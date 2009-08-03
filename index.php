<?php

/**
 * Wolf CMS - Content Management Simplified. <http://www.wolfcms.org>
 * Copyright (C) 2009 Martijn van der Kleijn <martijn.niji@gmail.com>
 * Copyright (C) 2008 Philippe Archambault <philippe.archambault@gmail.com>
 *
 * This file is part of Wolf CMS.
 *
 * Wolf CMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Wolf CMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Wolf CMS.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Wolf CMS has made an exception to the GNU General Public License for plugins.
 * See exception.txt for details and the full text.
 */

//  Constants  ---------------------------------------------------------------

define('CMS_ROOT', dirname(__FILE__));
define('FROG_ROOT', CMS_ROOT); // DEFINED ONLY FOR BACKWARDS SUPPORT - to be taken out before 0.9.0
define('CORE_ROOT', CMS_ROOT.'/wolf');

define('APP_PATH', CORE_ROOT.'/app');

require_once(CORE_ROOT.'/utils.php');

$config_file = CMS_ROOT.'/config.php';

require_once($config_file);

// if you have installed wolf and see this line, you can comment it or delete it :)
if ( ! defined('DEBUG')) { header('Location: install/'); exit(); }

// Security checks -----------------------------------------------------------
if (DEBUG == false && isWritable($config_file)) {
    // Windows systems always have writable config files... skip those.
    if (substr(PHP_OS, 0, 3) != 'WIN') {
        echo '<html><head><title>Wolf CMS automatically disabled!</title></head><body>';
        echo '<h1>Wolf CMS automatically disabled!</h1>';
        echo '<p>Wolf CMS has been disabled as a security precaution.</p>';
        echo '<p><strong>Reason:</strong> the configuration file was found to be writable.</p>';
        echo '</body></html>';
        exit();
    }
}

//  Init  --------------------------------------------------------------------

define('BASE_URL', URL_PUBLIC . (endsWith(URL_PUBLIC, '/') ? '': '/') . (USE_MOD_REWRITE ? '': '?'));

require CORE_ROOT.'/Framework.php';

if (USE_PDO)
{
    try 
	{
        $__CMS_CONN__ = new PDO(DB_DSN, DB_USER, DB_PASS);
	} 
	catch (PDOException $error) 
	{
        die('DB Connection failed: '.$error->getMessage());
	}
    
    if ($__CMS_CONN__->getAttribute(PDO::ATTR_DRIVER_NAME) == 'mysql')
        $__CMS_CONN__->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
}
else
{
    require_once CORE_ROOT . '/libraries/DoLite.php';
    $__CMS_CONN__ = new DoLite(DB_DSN, DB_USER, DB_PASS);
}

// DEFINED ONLY FOR BACKWARDS SUPPORT - to be taken out before 0.9.0
$__FROG_CONN__ = $__CMS_CONN__;

Record::connection($__CMS_CONN__);
Record::getConnection()->exec("set names 'utf8'");

Setting::init();

use_helper('I18n');
I18n::setLocale(Setting::get('language'));

// run everything!
require APP_PATH.'/main.php';