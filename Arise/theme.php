<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: Arise/theme.php
| Author: J. Falk (Falk)
| Author: RobiNN
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
if (!defined('IN_FUSION')) {
    die('Access Denied');
}

require_once INCLUDES.'theme_functions_include.php';

define('THEME_BULLET', '&middot;');
define('HEADER_LINKS', TRUE);

function render_page() {
    $settings = fusion_get_settings();

    echo '<div class="main-container">';
        echo '<div id="header">';
            echo '<div class="clearfix">';
            echo '<div class="logo pull-left"><a href="'.BASEDIR.$settings['opening_page'].'"><img src="'.BASEDIR.$settings['sitebanner'].'" alt="Logo" class="img-responsive"/></a></div>';

            if (HEADER_LINKS == TRUE) {
                echo '<div id="header-links" class="pull-right">';

                    $downloads = function_exists('infusion_exists') ? infusion_exists('downloads') : db_exists(DB_PREFIX.'downloads');
                    if ($downloads) {
                        if (!defined('DOWNLOAD_LOCALE')) {
                            if (file_exists(INFUSIONS.'downloads/locale/'.LOCALESET.'downloads.php')) {
                                define('DOWNLOAD_LOCALE', INFUSIONS.'downloads/locale/'.LOCALESET.'downloads.php');
                            } else {
                                define('DOWNLOAD_LOCALE', INFUSIONS.'downloads/locale/English/downloads.php');
                            }
                        }
                        echo '<a class="link" href="'.INFUSIONS.'downloads/downloads.php"><img class="img-responsive" src="'.THEME.'images/headerimgs/downloads.png" alt="'.fusion_get_locale('download_1000', DOWNLOAD_LOCALE).'"/><span>'.fusion_get_locale('download_1000', DOWNLOAD_LOCALE).'</span></a>';
                    }

                    $articles = function_exists('infusion_exists') ? infusion_exists('articles') : db_exists(DB_PREFIX.'articles');
                    if ($articles) {
                        echo '<a class="link" href="'.INFUSIONS.'articles/articles.php"><img class="img-responsive" src="'.THEME.'images/headerimgs/articles.png" alt="'.fusion_get_locale('article_0000', ARTICLE_LOCALE).'"/><span>'.fusion_get_locale('article_0000', ARTICLE_LOCALE).'</span></a>';
                    }

                    $gallery = function_exists('infusion_exists') ? infusion_exists('gallery') : db_exists(DB_PREFIX.'photos');
                    if ($gallery) {
                        echo '<a class="link" href="'.INFUSIONS.'gallery/gallery.php"><img class="img-responsive" src="'.THEME.'images/headerimgs/gallery.png" alt="'.fusion_get_locale('465', GALLERY_LOCALE).'"/><span>'.fusion_get_locale('465', GALLERY_LOCALE).'</span></a>';
                    }

                    $faq = function_exists('infusion_exists') ? infusion_exists('faq') : db_exists(DB_PREFIX.'faqs');
                    if ($faq) {
                        echo '<a class="link" href="'.INFUSIONS.'faq/faq.php"><img class="img-responsive" src="'.THEME.'images/headerimgs/faq.png" alt="'.fusion_get_locale('faq_0000', FAQ_LOCALE).'"/><span>'.fusion_get_locale('faq_0000', FAQ_LOCALE).'</span></a>';
                    }

                    $forum = function_exists('infusion_exists') ? infusion_exists('forum') : db_exists(DB_PREFIX.'forums');
                    if ($forum) {
                        echo '<a class="link" href="'.INFUSIONS.'forum/index.php"><img class="img-responsive" src="'.THEME.'images/headerimgs/forum.png" alt="'.fusion_get_locale('forum_0001', FORUM_LOCALE).'"/><span>'.fusion_get_locale('forum_0001', FORUM_LOCALE).'</span></a>';
                    }

                    $contact = !empty(fusion_get_locale('CT_400', LOCALE.LOCALESET.'contact.php')) ? fusion_get_locale('CT_400', LOCALE.LOCALESET.'contact.php') : fusion_get_locale('400', LOCALE.LOCALESET.'contact.php');
                    echo '<a class="link m-r-0" href="'.BASEDIR.'contact.php"><img class="img-responsive" src="'.THEME.'images/headerimgs/contact.png" alt="'.$contact.'"/><span>'.$contact.'</span></a>';
                echo '</div>';
            }
            echo '</div>';

            echo showsublinks('', 'navbar-default', [
                'id'          => 'main-nav',
                'show_header' => TRUE,
                'searchbar'   => TRUE
            ]);
        echo '</div>';

        echo '<div id="main-box">';
            echo renderNotices(getNotices(['all', FUSION_SELF]));

            echo defined('AU_CENTER') && AU_CENTER ? AU_CENTER : '';
            echo showbanners(1);

            echo '<div class="row">';
                $content = ['sm' => 11, 'md' => 11, 'lg' => 11];
                $left    = ['sm' => 3,  'md' => 2,  'lg' => 2];
                $right   = ['sm' => 3,  'md' => 2,  'lg' => 2];

                if (defined('LEFT') && LEFT) {
                    $content['sm'] = $content['sm'] - $left['sm'];
                    $content['md'] = $content['md'] - $left['md'];
                    $content['lg'] = $content['lg'] - $left['lg'];
                }

                if (defined('RIGHT') && RIGHT) {
                    $content['sm'] = $content['sm'] - $right['sm'];
                    $content['md'] = $content['md'] - $right['md'];
                    $content['lg'] = $content['lg'] - $right['lg'];
                }

                if (defined('LEFT') && LEFT) {
                    echo '<div id="left-side" class="col-xs-12 col-sm-'.$left['sm'].'-5 col-md-'.$left['md'].'-5 col-lg-'.$left['lg'].'-5">';
                        echo LEFT;
                    echo '</div>';
                }

                $half_column = (defined('LEFT') && LEFT) || (defined('RIGHT') && RIGHT) ? '' : '-5';
                echo '<div id="main-content" class="col-xs-12 col-sm-'.$content['sm'].$half_column.' col-md-'.$content['md'].$half_column.' col-lg-'.$content['lg'].$half_column.'">';
                    echo defined('U_CENTER') && U_CENTER ? U_CENTER : '';

                    echo CONTENT;

                    echo defined('L_CENTER') && L_CENTER ? L_CENTER : '';

                    echo showbanners(2);
                echo '</div>';

                if (defined('RIGHT') && RIGHT) {
                    echo '<div id="right-side" class="col-xs-12 col-sm-'.$right['sm'].'-5 col-md-'.$right['md'].'-5 col-lg-'.$right['lg'].'-5">';
                        echo RIGHT;
                    echo '</div>';
                }
            echo '</div>';

            echo defined('BL_CENTER') && BL_CENTER ? BL_CENTER : '';

            echo '<div class="row m-t-10">';
                echo defined('USER1') && USER1 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER1.'</div>' : '';
                echo defined('USER2') && USER2 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER2.'</div>' : '';
                echo defined('USER3') && USER3 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER3.'</div>' : '';
                echo defined('USER4') && USER4 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER4.'</div>' : '';
            echo '</div>';

            echo showFooterErrors();

        echo '</div>'; // #main-content

        echo '<footer id="main-footer" class="text-center">';
            echo '<div class="footer">';
                echo '<span class="pull-left">Arise Theme by <a href="https://www.php-fusion.co.uk" target="_blank">J. Falk (Falk)</a>, Ported for v9 by <a href="https://github.com/RobiNN1" target="_blank">RobiNN</a></span>';

                if ($settings['rendertime_enabled'] == 1 || $settings['rendertime_enabled'] == 2) {
                    echo '<small>'.showrendertime().showMemoryUsage().'</small>';
                }

                echo '<span class="pull-right">'.showcounter().'</span>';
            echo '</div>';

            echo '<div class="copyright">';
                echo stripslashes(strip_tags($settings['footer'])).'<br/>';
                echo showcopyright();
                echo showprivacypolicy();
            echo '</div>';
        echo '</footer>';

    echo '</div>';
}

function opentable($title = FALSE, $class = '') {
    echo '<div class="opentable box panel panel-default '.$class.'">';
    echo '<div class="panel-heading">'.$title.'</div>';
    echo '<div class="panel-body">';
}

function closetable() {
    echo '</div>';
    echo '</div>';
}

function openside($title = FALSE, $class = '') {
    echo '<aside class="openside box panel panel-default '.$class.'">';
    echo !empty($title) ? '<div class="panel-heading">'.$title.'</div>' : '';
    echo '<div class="panel-body">';
}

function closeside() {
    echo '</div>';
    echo '</aside>';
}
