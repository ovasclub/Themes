<?php

if (!defined("IN_FUSION")) { die("Access Denied"); }

require_once INFUSIONS."commerce/infusion_db.php";

$settings = fusion_get_settings();

include COMMERCE_LOCALE;

$inf_title = "Commerce";
$inf_description = "A standard E-commerce System.";
$inf_version = "1.0";
$inf_developer = "Guildsquare, Envision, PHP-Fusion Inc";
$inf_email = "php-fusion@php-fusion.co.uk";
$inf_weburl = "https://www.php-fusion.co.uk";
$inf_rights = "cms";
$inf_folder = "commerce";
$inf_image = "icon.png";

/**
 * Administration links
 */
$inf_adminpanel[] = array(
	"title" => $inf_title,
    "page" => 1,
	"image" => $inf_image,
	"panel" => "administration/index.php",
	"rights" => $inf_rights,
);

/**
 * Multilanguage Table
 */

$inf_mlt[] = array(
    "title" => $inf_title,
    "rights" => $inf_rights,
);

/**
 * Front end site links
 */

$enabled_languages = makefilelist(LOCALE, ".|..", TRUE, "folders");

if (!empty($enabled_languages)) {
    foreach ($enabled_languages as $language) {
        $locale_file = SHOP."locale/".$language.".php";
        if (file_exists($locale_file)) {
            $mlt_insertdbrow[$language][] = DB_SITE_LINKS." (link_name, link_url, link_visibility, link_position, link_window, link_order, link_language)
            VALUES ('Store', 'infusions/store/', '0', '2', '0', '2', '".$language."')";
            $mlt_deldbrow[$language][] = DB_SITE_LINKS." WHERE link_url='infusions/store/' AND link_language='".$language."'";
        }
    }
}

/**
 * Variations must be registered as type
 */
$inf_newtable[] = DB_STORE_PRODUCTS." (
id MEDIUMINT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
cid MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
status TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
title VARCHAR(50) NOT NULL DEFAULT '',
intro TEXT NOT NULL,
description TEXT NOT NULL,
user_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
user_ip VARCHAR(50) NOT NULL DEFAULT '0',
user_ip_type VARCHAR(50) NOT NULL DEFAULT '0',
language VARCHAR(100) NOT NULL DEFAULT '',
price BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
market_price BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
quantity MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
weight MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
shippable TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
free_shipping TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
access TINYINT(4) UNSIGNED NOT NULL DEFAULT '0',
datestamp INT(10) UNSIGNED NOT NULL DEFAULT '0',
allow_comments SMALLINT(1) UNSIGNED NOT NULL DEFAULT '0',
allow_ratings SMALLINT(1) UNSIGNED NOT NULL DEFAULT '0',
keywords TEXT NOT NULL,
PRIMARY KEY (id),
KEY parentid (cid)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";


/**
 * New modular to ensure product form can be loaded dynamically.
 * Customizable form and product attributes storage
 * Product module - to load module, if shop -- category, product, payment
 * // type - '0 = product', '1 = shipping', '2 = payment'
 */
$inf_newtable[] = DB_STORE_MODULES." (
module_id MEDIUMINT(9) UNSIGNED NOT NULL AUTO_INCREMENT,
module_title VARCHAR(100) NOT NULL DEFAULT '',
module_callback VARCHAR(200) NOT NULL DEFAULT '',
module_folder VARCHAR(100) NOT NULL DEFAULT '',
module_version VARCHAR(10) NOT NULL DEFAULT '',
PRIMARY KEY (module_id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

$inf_newtable[] = DB_STORE_MODULE_SETTINGS." (
settings_name VARCHAR(200) NOT NULL DEFAULT '',
settings_value TEXT NOT NULL,
settings_module VARCHAR(200) NOT NULL DEFAULT '',
PRIMARY KEY (settings_name),
KEY settings_module (settings_module)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

/**
 * Check completed
 */
$inf_newtable[] = DB_STORE_CATS." (
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

$inf_newtable[] = DB_STORE_BANNERS." (
banner_id MEDIUMINT(8) NOT NULL auto_increment,
banner_cid MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
banner_aid MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
banner_image VARCHAR(100) NOT NULL DEFAULT '',
banner_thumbnail VARCHAR(200) NOT NULL DEFAULT '',
banner_description TEXT NOT NULL,
banner_name MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
banner_type SMALLINT(1) UNSIGNED NOT NULL DEFAULT '0',
banner_status SMALLINT(1) UNSIGNED NOT NULL DEFAULT '0',
banner_start INT(10) UNSIGNED NOT NULL DEFAULT '0',
banner_end INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY  (banner_id),
KEY banner_name (banner_name),
KEY banner_cid (banner_cid)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

$inf_newtable[] = DB_STORE_PHOTOS." (
photo_id MEDIUMINT(8) UNSIGNED NOT NULL auto_increment,
album_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
photo_title VARCHAR(100) NOT NULL DEFAULT '',
photo_description text NOT NULL,
photo_filename VARCHAR(100) NOT NULL DEFAULT '',
photo_thumb1 VARCHAR(100) NOT NULL DEFAULT '',
photo_thumb2 VARCHAR(100) NOT NULL DEFAULT '',
photo_datestamp int(10) UNSIGNED NOT NULL DEFAULT '0',
photo_user MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
photo_views MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
photo_order SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
photo_allow_comments tinyint(1) UNSIGNED NOT NULL default '1',
photo_last_viewed int(10) UNSIGNED NOT NULL default '1',
PRIMARY KEY  (photo_id),
KEY photo_user (photo_user)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

// Require 3 more core tables
/**
 * tax , shipping, payment
 */


/**
 * This one is module for Digital store
 */
/*
$inf_newtable[] = DB_STORE_FILES." (
pfile_id MEDIUMINT(8) UNSIGNED NOT NULL auto_increment,
pfile_uid MEDIUMINT(8) UNSIGNED NOT NULL,
pfile_version VARCHAR(50) NOT NULL default '5',
pfile_file VARCHAR(20) NOT NULL DEFAULT '',
pfile_name VARCHAR(70) NOT NULL DEFAULT '',
pfile_downloaded MEDIUMINT(10) NOT NULL DEFAULT '0',
pfile_datestamp int(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (pfile_id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";
*/


/**
 * This one is module
 */
/*
$inf_newtable[] = DB_STORE_DOWNLOADS." (
oid MEDIUMINT(8) NOT NULL auto_increment,
ouid MEDIUMINT(8) NOT NULL,
oauid MEDIUMINT(8) NOT NULL,
oitems MEDIUMINT(8) NOT NULL,
oorder VARCHAR(255) NOT NULL DEFAULT '',
odate int(10) NOT NULL DEFAULT '0',
PRIMARY KEY  (oid),
KEY ouid (ouid)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";
*/

$store_settings = array(
    "product_per_page" => 16,
    "product_image_h" => 400,
    "product_image_w" => 400,
    "product_image_thumb_w" => 250,
    "product_image_thumb_h" => 250,
    "product_image_b" => 1500000,
    "photo_per_product" => 4,

    "featured_product_per_page" => 16,

    "category_image_h" => 250,
    "category_image_w" => 250,
    "category_image_thumb_h" => 250,
    "category_image_thumb_w" => 250,
    "category_image_b" => 1500000,
    "allow_cat_img" => 0,


    "allow_submissions" => 1,

    "currency" => "USD", // multicurrency
    "social_network" => 1, // opengraph support

    "enable_cookies" => 1, // use cookies
    "newtime" => 604800,
);

foreach($store_settings as $key => $value) {
    $inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('$key', '$value', '$inf_rights')";
}

/*
$inf_insertdbrow[1] = DB_ADDONS_SETTINGS." (
cats, cat_disp, nopp, noppf, target,
folderlink, selection, cookies, bclines, icons, statustext, closesamelevel, inorder, shopmode, returnpage,
ppmail, ipr, ratios, idisp_h, idisp_w, idisp_h2, idisp_w2,
catimg_w, catimg_h, image_w, image_h, image_b, image_tw, image_th, image_t2w, image_t2h,
buynow_color, checkout_color, cart_color, addtocart_color, info_color, return_color,
pretext, pretext_w, listprice, currency, shareing,
weightscale, vat, vat_default, terms, itembox_w, itembox_h, cipr, newtime, freADBipsum, version) VALUES
('0', '0', 6, 9, '_self',0, 1, 1, 1, 1, 1, 1, 0, '1', 'ordercompleted.php', 'donations@php-fusion.co.uk', 3, '1',
'130', '100', '180', '250', '100', '100', '6400', '6400', '9999999', '150', '100', '250', '250', 'blue', 'green', 'red',
'magenta', 'orange', 'yellow', '0', '190px', '1', 'EUR', '1', 'KG', 25, '0',
'<h2> Ordering </h2><br />\r\nWhilst all efforts are made to ensure accuracy of description,
specifications and pricing there may <br />be occasions where errors arise.
Should such a situation occur [Company name] cannot accept your order. <br />
In the event of a mistake you will be contacted with a full explanation and a corrected offer.
<br />The information displayed is considered as an invitation to treat not as a confirmed offer for sale.
\r\nThe contract is confirmed upon supply of goods.\r\n<br /><br /><br />\r\n<h2>Delivery and Returns</h2><br />
\r\n[Company name] returns policy has been set up to keep costs down and to make the process as easy for you as possible.
You must contact us and be in receipt of a returns authorisation (RA) number before sending any item back. Any product without
a RA number will not be refunded. <br /><br /><br />\r\n<h2> Exchange </h2><br />\r\n If when you receive your product(s), you are
not completely satisfied you may return the items to us, within seven days of exchange or refund. Returns will take approximately
5 working days for the process once the goods have arrived. Items must be in original packaging, in all original boxes,
packaging materials, manuals blank warranty cards and all accessories and documents provided by the manufacturer.<br /><br /><br />\r\n\r\n.
If our labels are removed from the product â€“ the warranty becomes void.<br /><br /><br />\r\n\r\n
We strongly recommend that you fully insure your package that you are returning. We suggest the use of a carrier that can provide
you with a proof of delivery. [Company name] will not be held responsible for items lost or damaged in transit.<br /><br />
<br />\r\n\r\nAll shipping back to [Company name] is paid for by the customer. We are unable to refund you postal fees.<br />
<br /><br />\r\n\r\nAny product returned found not to be defective can be refunded within the time stated above and will be subject
to a 15% restocking fee to cover our administration costs. Goods found to be tampered with by the customer will not be replaced but
returned at the customers expense. <br /><br /><br />\r\n\r\n If you are returning items for exchange please be aware that a second charge may apply. <br /><br /><br />\r\n\r\n<h2>Non-Returnable </h2><br />\r\n For reasons of hygiene and public health, refunds/exchanges are not available for used ......... (this does not apply to faulty goods â€“ faulty products will be exchanged like for like)<br /><br /><br />\r\n\r\nDiscounted or our end of line products can only be returned for repair no refunds of replacements will be made.<br /><br /><br />\r\n\r\n<h2> Incorrect/Damaged Goods </h2><br />\r\n\r\n We try very hard to ensure that you receive your order in pristine condition. If you do not receive your products ordered. Please contract us. In the unlikely event that the product arrives damaged or faulty, please contact [Company name] immediately, this will be given special priority and you can expect to receive the correct item within 72 hours. Any incorrect items received all delivery charges will be refunded back onto you credit/debit card.<br /><br /><br />\r\n\r\n<h2>Delivery service</h2><br />\r\nWe try to make the delivery process as simple as possible and our able to send your order either you home or to your place of work.<br /><br /><br />\r\n\r\nDelivery times are calculated in working days Monday to Friday. If you order after 4 pm the next working day will be considered the first working day for delivery. In case of bank holidays and over the Christmas period, please allow an extra two working days.<br /><br /><br />\r\n\r\nWe aim to deliver within 3 working days but sometimes due to high order volume certain in sales periods please allow 4 days before contacting us. We will attempt to email you if we become aware of an unexpected delay. <br /><br /><br />\r\n\r\nAll small orders are sent out via royal mail 1st packets post service, if your order is over Â£15.00 it will be sent out via royal mails recorded packet service, which will need a signature, if you are not present a card will be left to advise you to pick up your goods from the local sorting office.<br /><br /><br />\r\n\r\nEach item will be attempted to be delivered twice. Failed deliveries after this can be delivered at an extra cost to you or you can collect the package from your local post office collection point.<br /><br /><br />\r\n\r\n<h2>Export restrictions</h2><br /><br /><br />\r\n\r\nAt present [Company name] only sends goods within the [Country]. We plan to add exports to our services in the future. If however you have a special request please contact us your requirements.<br /><br /><br />\r\n\r\n<h2> Privacy Notice </h2><br />\r\n\r\nThis policy covers all users who register to use the website. It is not necessary to purchase anything in order to gain access to the searching facilities of the site.<br /><br /><br />\r\n\r\n<h2> Security </h2><br />\r\nWe have taken the appropriate measures to ensure that your personal information is not unlawfully processed. [Company name] uses industry standard practices to safeguard the confidentiality of your personal identifiable information, including firewalls and secure socket layers. <br /><br /><br />\r\n\r\nDuring the payment process, we ask for personal information that both identifies you and enables us to communicate with you. <br /><br /><br />\r\n\r\nWe will use the information you provide only for the following purposes.<br /><br /><br />\r\n\r\n* To send you newsletters and details of offers and promotions in which we believe you will be interested. <br />\r\n* To improve the content design and layout of the website. <br />\r\n* To understand the interest and buying behavior of our registered users<br />\r\n* To perform other such general marketing and promotional focused on our products and activities. <br />\r\n\r\n<h2> Conditions Of Use </h2><br />\r\n[Company name] and its affiliates provide their services to you subject to the following conditions. If you visit our shop at [Company name] you accept these conditions. Please read them carefully, [Company name] controls and operates this site from its offices within the [Country]. The laws of [Country] relating to including the use of, this site and materials contained. <br /><br /><br />\r\n\r\nIf you choose to access from another country you do so on your own initiave and are responsible for compliance with applicable local lands. <br /><br /><br />\r\n\r\n<h2> Copyrights </h2><br />\r\nAll content includes on the site such as text, graphics logos button icons images audio clips digital downloads and software are all owned by [Company name] and are protected by international copyright laws. <br /><br /><br />\r\n\r\n<h2> License and Site Access </h2><br />\r\n[Company name] grants you a limited license to access and make personal use of this site. This license doses not include any resaleâ€™s of commercial use of this site or its contents any collection and use of any products any collection and use of any product listings descriptions or prices any derivative use of this site or its contents, any downloading or copying of account information. For the benefit of another merchant or any use of data mining, robots or similar data gathering and extraction tools.<br /><br /><br />\r\n\r\nThis site may not be reproduced duplicated copied sold â€“ resold or otherwise exploited for any commercial exploited without written consent of [Company name].<br /><br /><br />\r\n\r\n<h2> Product Descriptions </h2><br />\r\n[Company name] and its affiliates attempt to be as accurate as possible however we do not warrant that product descriptions or other content is accurate complete reliable, or error free.<br /><br /><br />\r\nFrom time to time there may be information on [Company name] that contains typographical errors, inaccuracies or omissions that may relate to product descriptions, pricing and availability.<br /><br /><br />\r\n
We reserve the right to correct ant errors inaccuracies or omissions and to change or update information at any time without prior notice.
(Including after you have submitted your order) We apologies for any inconvenience this may cause you. <br /><br /><br />\r\n\r\n
<h2> Prices </h2><br />\r\nPrices and availability of items are subject to change without notice the prices advertised on this site are for
orders placed and include VAT and delivery.<br /><br /><br />\r\n<br /><br /><br />\r\nPlease review our other policies posted on this site. These policies also govern your visit to [Company name]',
'200px', '300px', '3','604800','0','1.0')";
$inf_droptable[3] = DB_ADDONS_SETTINGS;
*/

$inf_droptable[] = DB_STORE_CATS;
$inf_droptable[] = DB_STORE_PRODUCTS;
$inf_droptable[] = DB_STORE_PHOTOS;
$inf_droptable[] = DB_STORE_BANNERS;
$inf_droptable[] = DB_STORE_MODULES;
$inf_droptable[] = DB_STORE_MODULE_SETTINGS;

/*$inf_droptable[] = DB_STORE_FILES;
$inf_droptable[] = DB_STORE_DOWNLOADS; */


$inf_deldbrow[] = DB_COMMENTS." WHERE comment_type='$inf_rights'";

$inf_deldbrow[] = DB_RATINGS." WHERE rating_type='$inf_rights'";

$inf_deldbrow[] = DB_SETTINGS_INF." WHERE settings_inf='$inf_rights'";

$inf_deldbrow[] = DB_SETTINGS_INF." WHERE settings_inf='SHOP'";