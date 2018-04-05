<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: profile.php
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
$this->right_off = TRUE;
$this->display_mode = 'single';

function display_user_profile($info) {
    add_to_head("<link href='".THEMES."templates/global/css/profile.css' rel='stylesheet'/>");
    ?>
    <!--userprofile_pre_idx-->
    <section id='user-profile' class='spacer-sm overflow-hide'>
        {%tab_header%}
        <div class='spacer-sm'>
            <div class='clearfix p-15 p-t-0'>
                <div class='pull-left m-r-10'>{%user_avatar%}</div>
                <div class='overflow-hide'>
                    <h4 class='m-0'>{%user_name%}<br/>
                        <small>{%user_level%}</small>
                    </h4>
                </div>
            </div>
            <div class='clearfix'>{%admin_buttons%}</div>
            <hr/>
            <div class='clearfix'>{%basic_info%}</div>
            <hr/>
            <div class='clearfix'>{%extended_info%}</div>
            <div class='text-center'>{%buttons%}</div>
        </div>
        {%tab_footer%}
    </section>
    <!--userprofile_sub_idx-->
    <?php
}
