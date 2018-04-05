<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: news.php
| Author: Frederick MC Chan (Chan)
| Co-Author: RobiNN
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
$this->set_display_mode('full-grid');
$this->right_off = FALSE;

function display_main_news($info) {
    $news_settings = \PHPFusion\News\NewsServer::get_news_settings();
    $locale = fusion_get_locale();

    add_to_head("<link href='".INFUSIONS."news/templates/css/news.css' rel='stylesheet'/>\n");
    add_to_head("<script type='text/javascript' src='".INCLUDES."jquery/jquery.cookie.js'></script>");

    $cookie_expiry = time() + 7 * 24 * 3600;
    if (empty($_COOKIE['fusion_news_view'])) {
        setcookie("fusion_news_view", 1, $cookie_expiry);
    } else if (isset($_GET['switchview']) && isnum($_GET['switchview'])) {
        setcookie("fusion_news_view", intval($_GET['switchview'] == 2 ? 2 : 1), $cookie_expiry);
        redirect(INFUSIONS.'news/news.php');
    }

    opentable($locale['news_0004']);
    echo render_breadcrumbs();

    /* Slideshow */
    $carousel_indicators = '';
    $carousel_item = '';
    $res = 0;
    $carousel_height = "300";
    if (!empty($info['news_items'])) {
        $i = 0;
        foreach ($info['news_items'] as $news_item) {

            if ($news_item['news_image_src'] && file_exists($news_item['news_image_src'])) {
                $carousel_active = $res == 0 ? 'active' : '';
                $res++;
                $carousel_indicators .= "<li data-target='#news-carousel' data-slide-to='$i' class='".$carousel_active."'></li>\n";
                $carousel_item .= "<div class='item ".$carousel_active."'>\n";
                $carousel_item .= "<img class='img-responsive' style='position:absolute; width:100%;' src='".$news_item['news_image_src']."' alt='".$news_item['news_subject']."'>\n";
                $carousel_item .= "
                    <div class='carousel-caption'>
                        <div class='overflow-hide'>
                        <a class='text-white' href='".INFUSIONS."news/news.php?readmore=".$news_item['news_id']."'><h4 class='text-white m-t-10'>".$news_item['news_subject']."</h4></a>\n
                        <span class='news-carousel-action m-r-10'><i class='fa fa-eye fa-fw'></i>".$news_item['news_reads']."</span>
                        ".($news_item['news_allow_comments'] ? "<span class='m-r-10'>".display_comments($news_item['news_comments'],
                            INFUSIONS."news/news.php?readmore=".$news_item['news_id']."#comments")."</span>" : '')."
                        ".($news_item['news_allow_ratings'] ? "<span class='m-r-10'>".display_ratings($news_item['news_sum_rating'],
                            $news_item['news_count_votes'],
                            INFUSIONS."news/news.php?readmore=".$news_item['news_id']."#postrating")." </span>" : '')."
                        </div>\n
                    </div>\n</div>\n
                    ";
                $i++;
            }
        }
    }

    if ($res) {
        echo "<div id='news-carousel' class='carousel slide'  data-interval='20000' data-ride='carousel'>\n";
        if ($res > 1) {
            echo "<ol class='carousel-indicators'>\n";
            echo $carousel_indicators;
            echo "</ol>";
        }
        echo "<div class='carousel-inner' style='height:".$carousel_height."px' role='listbox'>\n";
        echo $carousel_item;
        echo "</div>\n";
        echo "
                <a class='left carousel-control' href='#news-carousel' role='button' data-slide='prev'>
                    <span class='glyphicon glyphicon-chevron-left' aria-hidden='true'></span>
                    <span class='sr-only'>".$locale['previous']."</span>
                </a>
                <a class='right carousel-control' href='#news-carousel' role='button' data-slide='next'>
                    <span class='glyphicon glyphicon-chevron-right' aria-hidden='true'></span>
                    <span class='sr-only'>".$locale['next']."</span>
                </a>\n
                ";
        echo "</div>\n";
    }

    echo "<div class='panel panel-default panel-news-header m-t-20'>\n";
    echo "<div class='panel-body'>\n";
    echo "<div class='pull-right'>\n";
    echo "<a class='btn btn-sm btn-default' href='".INFUSIONS."news/news.php'><i class='fa fa-desktop fa-fw'></i> ".$locale['news_0004']."</a>\n";
    echo "<button type='button' class='btn btn-sm btn-primary' data-toggle='collapse' data-target='#newscat' aria-expanded='true' aria-controls='newscat'><i class='fa fa-newspaper-o'></i> ".$locale['news_0009']."</button>\n";
    echo "</div>\n";
    echo "<div class='pull-left m-r-10' style='position:relative; margin-top:-30px;'>\n";
    echo "<div style='max-width:80px;'>\n";
    echo $info['news_cat_image'];
    echo "</div>\n";
    echo "</div>\n";
    echo "<div class='overflow-hide'>\n";
    echo "<h3 class='display-inline text-dark'>".$info['news_cat_name']."</h3><br/><span class='strong'>".$locale['news_0008'].":</span> <span class='text-dark'>\n
            ".(!empty($info['news_last_updated']) ? $info['news_last_updated'] : $locale['na'])."</span>";
    echo "</div>\n";
    echo "</div>\n";

    echo "<div id='newscat' class='panel-collapse collapse m-b-10'>\n";
    echo "<!--pre_news_cat_idx-->";
    echo "<ul class='list-group'>\n";
    echo "<li class='list-group-item'><hr class='m-t-0 m-b-5'>\n";
    echo "<span class='display-inline-block m-b-10 strong text-smaller text-uppercase'> ".$locale['news_0010']."</span><br/>\n";

    if (is_array($info['news_categories'][0])) {
        foreach ($info['news_categories'][0] as $cat_id => $cat_data) {
            echo isset($_GET['cat_id']) && $_GET['cat_id'] == $cat_id ? '' : "<a href='".INFUSIONS."news/news.php?cat_id=".$cat_id."' class='btn btn-sm btn-default'>".$cat_data['name']."</a>";
        }

        if (!empty($info['news_categories'][1]) && is_array($info['news_categories'][1])) {
            foreach ($info['news_categories'][1] as $cat_id => $cat_data) {
                echo isset($_GET['cat_id']) && $_GET['cat_id'] == $cat_id ? '' : "<a href='".INFUSIONS."news/news.php?cat_id=".$cat_id."' class='btn btn-sm btn-default'>".$cat_data['name']."</a>";
            }
        }

    } else {
        echo "<p>".$locale['news_0016']."</p>";
    }
    echo "</li>";
    echo "</ul>\n";

    echo "<!--sub_news_cat_idx-->\n";
    echo "</div>\n</div>\n";

    echo "<div class='row m-b-20 m-t-20'>\n";
    echo "<div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>\n";

    $active = isset($_COOKIE['fusion_news_view']) && isnum($_COOKIE['fusion_news_view']) && $_COOKIE['fusion_news_view'] == 2 ? 2 : 1;
    echo "<div class='btn-group pull-right display-inline-block m-l-10'>\n";
    echo "<a class='btn btn-default snv".($active == 1 ? ' active ' : '')."' href='".INFUSIONS."news/news.php?switchview=1'><i class='fa fa-th-large'></i> ".$locale['news_0014']."</a>";
    echo "<a class='btn btn-default snv".($active == 2 ? ' active ' : '')."' href='".INFUSIONS."news/news.php?switchview=2'><i class='fa fa-bars'></i> ".$locale['news_0015']."</a>";
    echo "</div>\n";

    // Filters
    echo "<div class='display-inline-block'>\n";
    echo "<span class='text-dark strong m-r-10'>".$locale['show']." :</span>";
    $i = 0;
    foreach ($info['news_filter'] as $link => $title) {
        $filter_active = (!isset($_GET['type']) && $i == '0') || isset($_GET['type']) && stristr($link, $_GET['type']) ? 'text-dark strong' : '';
        echo "<a href='".$link."' class='display-inline $filter_active m-r-10'>".$title."</a>";
        $i++;
    }
    echo "</div>\n";
    // end filter.
    echo "</div>\n</div>\n";

    $news_span = $active == 2 ? 12 : 4;

    if (!empty($info['news_items'])) {
        echo "<div class='row'>\n";
        foreach ($info['news_items'] as $i => $news_info) {
            echo "<div class='col-xs-12 col-sm-$news_span col-md-$news_span col-lg-$news_span'>\n";
            echo (isset($_GET['cat_id'])) ? "<!--pre_news_cat_idx-->\n" : "<!--news_prepost_".$i."-->\n";
            render_news($news_info['news_subject'], $news_info['news_news'], $news_info, $active == 2);
            echo (isset($_GET['cat_id'])) ? "<!--sub_news_cat_idx-->" : "<!--sub_news_idx-->\n";
            echo "</div>\n";
        }
        echo "</div>\n";

        if ($info['news_total_rows'] > $news_settings['news_pagination']) {
            $type_start = isset($_GET['type']) ? "type=".$_GET['type']."&amp;" : '';
            $cat_start = isset($_GET['cat_id']) ? "cat_id=".$_GET['cat_id']."&amp;" : '';
            echo "<div class='text-center m-t-10 m-b-10'>".makepagenav($_GET['rowstart'],
                    $news_settings['news_pagination'],
                    $info['news_total_rows'], 3,
                    INFUSIONS."news/news.php?".$cat_start.$type_start)."</div>\n";
        }
    } else {
        echo "<div class='well text-center'>".$locale['news_0005']."</div>\n";
    }

    closetable();

}
