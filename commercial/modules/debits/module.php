<?php
$module_title = "Debits";
$module_description = "Prepaid solutions for commerce core";
$module_version = "1.0";
$module_folder = "debits";
$module_developer = "Envision";
$module_weburl = "https://www.php-fusion.co.uk";
$module_email = "support@php-fusion.co.uk";
$module_icon = "icon.png";

$module_newtable[] = DB_STORE_CATS." (
cid MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
parentid MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
title VARCHAR(45) NOT NULL DEFAULT '',
description TEXT NOT NULL,
access TINYINT(4) UNSIGNED NOT NULL DEFAULT '0',
image VARCHAR(45) NOT NULL DEFAULT '',
image_thumb VARCHAR(45) NOT NULL DEFAULT '',
status SMALLINT(1) UNSIGNED NOT NULL DEFAULT '0',
language TEXT NOT NULL,
orderby VARCHAR(50) NOT NULL DEFAULT '',
ordernum MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (cid)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";
