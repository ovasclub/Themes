<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: edit_profile.php
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
// Edit profile
function render_userform($info) {
    global $theme;
    // page navigation
    $theme->display_mode = 'single';
    $theme->right_off = TRUE;
    closetable();

    if (isset($info['section']) && !empty($info['section'])) {
        $title = [];
        foreach ($info['section'] as $id => $tab_data) {
            $title[] = ['url' => $tab_data['link'], 'title' => $tab_data['name']];
        }
        $field_query = dbquery("SELECT field_cat_id FROM ".DB_USER_FIELD_CATS." WHERE field_parent='0'");
        while ($fdata = dbarray($field_query)) {
            $theme->title_array[] = $fdata['field_cat_id'];
        }
        $theme->sub_horizontal_nav($title, ['get' => 'profiles']);
    }
    if (isset($_GET['profiles']) && $_GET['profiles'] == 1) {
        echo "<div class='m-t-20'>\n";
        echo $info['openform'];
        echo $info['user_name'];
        echo $info['user_email'];
        echo $info['user_hide_email'];
        echo $info['user_avatar'];
        echo $info['user_password'];
        if (iADMIN)
            echo $info['user_admin_password'];
        if (isset($info['user_field']))
            echo $info['user_field'];
        echo isset($info['validate']) ? $info['validate'] : '';
        echo isset($info['terms']) ? $info['terms'] : '';
        echo $info['button'];
        echo $info['closeform'];
        echo "</div>\n";
    } else {
        echo $info['openform'];
        if (isset($info['user_field'])) {
            echo $info['user_field'];
        }
        echo $info['button'];
        echo $info['closeform'];
    }
}
