<?php

require_once __DIR__."/model/attributes_resource.php";

$module_title = "Classes & Attributes";

$module_description = "This module allows you to add product classes, modifiers and custom attributes and configure your own configurable presets.\n\r
Product - Enable you to have a new product specification page in every product with attributes.\n\r
Category - Enable you assign a specific group of default form fields whenever you add a product\n\r
Settings - Enable you to build a presets of fields.\n\r
Modifiers - Enable you to set custom modifiers to price and weight of each product\n\r
Attributes - Fields can be multiple or singular values\n\r
Multi-Language - Uses Multilanguage Native PHP-Fusion 9 Multi language Module \n\r
";

$module_version = "1.0";

$module_folder = "attributes";

$module_developer = "Frederick MC Chan";

$module_weburl = "https://www.php-fusion.co.uk";

$module_email = "support@php-fusion.co.uk";

$module_icon = "icon.png";

$module_callback = "field_attributes"; // the class name that is reserved for this module

// need to register feature when install to inf_module and type

// will need to auto register against DB_STORE_MODULES - then build a cache everytime front end runs

$module_newtable[] = DB_COM_FIELD_CLASSES." (
field_class_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
field_class_title TEXT NOT NULL,
PRIMARY KEY (field_class_id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

/**
 * Grouping of fields
 * field_cat_id - Primary Key
 * field_cat_parent - the class it belongs to
 * field_cat_title - The title of this field
 * field_cat_order - The ordering on display of listed filter
 */

$module_newtable[] = DB_COM_FIELD_CATS." (
field_cat_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
field_cat_class MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
field_cat_title TEXT NOT NULL,
field_cat_order MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (field_cat_id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";


/**
 * field_id - the field unique primary key
 * field_title - For example "Size"
 * field_description - the field description helper text
 * field_type - Quantum build Type - select 1, multiple checkbox or 1/0, textarea - for render purposes only that affects its ORDER table
 * field_language - the path to any upload folder
 * field_order - ordering of the field inside the group
 */

$module_newtable[] = DB_COM_FIELDS." (
field_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
field_cat MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
field_class MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
field_title TEXT NOT NULL,
field_description TEXT NOT NULL,
field_type TINYINT(1) NOT NULL DEFAULT '0',
field_language TEXT NOT NULL,
field_order MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (field_id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

/**
 * Option for fields
 * autolink to this table if type has options.. well all field has options
 *
 * field_option_id - Primary Key
 * field_option_parent - the field id
 * field_option_title - the title
 * field_option_value - the value of this option (if is checkbox, option is not used. if is select or checkboxes multiplier should stack)
 * field_option_price_modifiers - if this option is selected has what kind of price affect
 * field_option_weight_modifier - if this option is selected has what kind of weight affect
 * field_option_order the ordering of the option
 */
$module_newtable[] = DB_COM_FIELD_OPTIONS." (
field_option_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
field_option_parent MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
field_option_value TEXT NOT NULL,
field_option_price_modifier TEXT NOT NULL,
field_option_weight_modifier TEXT NOT NULL,
field_option_order MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (field_option_id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

/**
 * field_order_id - the bill (unique)
 * field_product_id - the product id (unique)
 * field_parent - the field containing the value selected
 */
$module_newtable[] = DB_COM_FIELD_ORDERS." (
field_order_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
field_product_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
field_parent MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
field_value TEXT NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

$module_droptable[] = DB_COM_FIELD_CLASSES;

$module_droptable[] = DB_COM_FIELD_CATS;

$module_droptable[] = DB_COM_FIELDS;

$module_droptable[] = DB_COM_FIELD_ORDERS;

// Modify Category Table on Install
$module_newcol[] = array(
  "table"=>DB_STORE_CATS,
  "column" => "class",
  "column_type" => "MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0'"
);

// Modify Category Table on Uninstall
$module_dropcol[] = array(
  "table" => DB_STORE_CATS,
  "column" => "class"
);