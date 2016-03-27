<?php

/**
 * Path definitions
 * Bundle 1 - store front end
 */
if (!defined("STORE")) define("STORE", INFUSIONS."store/");
if (!defined("STORE_HOME")) define("STORE_HOME", INFUSIONS."store/index.php");

//if (!defined("PRODUCT_IMAGE")) define("PRODUCT_IMAGE", INFUSIONS."store/img/screenshots/");
//if (!defined("STORE_SCREEN")) define("STORE_SCREEN", INFUSIONS."store/img/screenshots/");

//if (!defined("STORE_IMG")) define("STORE_IMG", INFUSIONS."store/img/");


/**
 * Bundle 2 - commerce back end
 */
if (!defined("COMMERCE")) define("COMMERCE", INFUSIONS."commerce/");

if (!defined("COMMERCE_LOCALE")) {
    if (file_exists(INFUSIONS."commerce/locale/".LOCALESET.".php")) {
        define("COMMERCE_LOCALE", INFUSIONS."commerce/locale/".LOCALESET.".php");
    } else {
        define("COMMERCE_LOCALE", INFUSIONS."commerce/locale/English.php");
    }
}

if (!defined("COMMERCE_MODULES")) define("COMMERCE_MODULES", INFUSIONS."commerce/modules/");

include INCLUDES."infusions_include.php";
