<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright � 2002 - 2013 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: infusion_db.php
| Version: 1.0
| Author: PHP-Fusion Development Team
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
if (!defined("IN_FUSION")) { die("Access Denied"); }

if (defined("ADMIN_PANEL")) { $admin->setAdminPageIcons("COM", "<i class='fa fa-shopping-basket'></i>"); }

if (!defined("DB_STORE_CATS")) define("DB_STORE_CATS", DB_PREFIX."store_cats");
if (!defined("DB_STORE_PRODUCTS")) define("DB_STORE_PRODUCTS", DB_PREFIX."store_products");
if (!defined("DB_STORE_BANNERS")) define("DB_STORE_BANNERS", DB_PREFIX."store_banners");
if (!defined("DB_STORE_MODULES")) define("DB_STORE_MODULES", DB_PREFIX."store_modules");
if (!defined("DB_STORE_MODULE_SETTINGS")) define("DB_STORE_MODULE_SETTINGS", DB_PREFIX."store_module_settings");
if (!defined("DB_STORE_CART")) define("DB_STORE_CART", DB_PREFIX."store_cart");
if (!defined("DB_STORE_PHOTOS")) define("DB_STORE_PHOTOS", DB_PREFIX."store_photos");
if (!defined("DB_STORE_FILES")) define("DB_STORE_FILES", DB_PREFIX."store_files");
if (!defined("DB_STORE_DOWNLOADS")) define("DB_STORE_DOWNLOADS", DB_PREFIX."store_downloads");
if (!defined("DB_STORE_VERSIONS")) define("DB_STORE_VERSIONS", DB_PREFIX."store_versions");
if (!defined("DB_STORE_ASSIGN")) define("DB_STORE_ASSIGN", DB_PREFIX."store_assign");
if (!defined("DB_STORE_ADDONVERSIONS")) define("DB_STORE_ADDONVERSIONS", DB_PREFIX."store_addonversions");

require_once __DIR__."/commerce_resource.php";