<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: login.php
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
$this->left_off = TRUE;
$this->display_mode = 'single';

function render_template() {
    global $locale, $userdata, $settings, $aidlink;
    include THEME_LOCALE.'login.php';

    if (iMEMBER) {
        $msg_count = dbcount("(message_id)", DB_MESSAGES, "message_to='".$userdata['user_id']."' AND message_read='0' AND message_folder='0'");
        opentable($userdata['user_name']);
        echo "<div style='text-align:center'><br />\n";
        echo THEME_BULLET." <a href='".BASEDIR."edit_profile.php' class='side'>".$locale['global_120']."</a><br />\n";
        echo THEME_BULLET." <a href='".BASEDIR."messages.php' class='side'>".$locale['global_121']."</a><br />\n";
        echo THEME_BULLET." <a href='".BASEDIR."members.php' class='side'>".$locale['global_122']."</a><br />\n";
        if (iADMIN && (iUSER_RIGHTS != "" || iUSER_RIGHTS != "C")) {
            echo THEME_BULLET." <a href='".ADMIN."index.php".$aidlink."' class='side'>".$locale['global_123']."</a><br />\n";
        }
        echo THEME_BULLET." <a href='".BASEDIR."index.php?logout=yes' class='side'>".$locale['global_124']."</a>\n";
        if ($msg_count) {
            echo "<br /><br />\n";
            echo "<strong><a href='".BASEDIR."messages.php' class='side'>".sprintf($locale['global_125'], $msg_count);
            echo ($msg_count == 1 ? $locale['global_126'] : $locale['global_127'])."</a></strong>\n";
        }
        echo "<br /><br /></div>\n";
    } else {

        $action_url = $settings['opening_page'];
        opentable($locale['sh_0100'].' '.$settings['sitename']);
        echo "<p>".$locale['sh_0101']."</p>\n";

        if (isset($_GET['error']) && isnum($_GET['error'])) {
            if (isset($_GET['redirect']) && strpos(urldecode($_GET['redirect']), "/") === 0) {
                $action_url = cleanurl(urldecode($_GET['redirect']));
            }
            echo "<div style='text-align:center;font-weight:bold'>";
            switch ($_GET['error']) {
                case 1:
                    echo $locale['global_196'];
                    break;
                case 2:
                    echo $locale['global_192'];
                    break;
                case 3:
                    if (isset($_COOKIE[COOKIE_PREFIX."user"])) {
                        redirect($action_url);
                    } else {
                        echo $locale['global_193'];
                    }
                    break;
                case 4:
                    if (isset($_GET['status']) && isnum($_GET['status'])) {
                        $id = ((isset($_GET['id']) && isnum($_GET['id'])) ? $_GET['id'] : "0");
                        switch ($_GET['status']) {
                            case 1:
                                $data = dbarray(dbquery("SELECT suspend_reason FROM ".DB_SUSPENDS."
                                WHERE suspended_user='".$id."'
                                ORDER BY suspend_date DESC  LIMIT 1"));
                                echo $locale['global_406']."<br /><br />".$data['suspend_reason'];
                                break;
                            case 2:
                                echo $locale['global_195'];
                                break;
                            case 3:
                                $data = dbarray(dbquery("SELECT u.user_actiontime, s.suspend_reason FROM ".DB_SUSPENDS." s
                                LEFT JOIN ".DB_USERS." u ON u.user_id=s.suspended_user
                                WHERE s.suspended_user='".$id."'
                                ORDER BY s.suspend_date DESC LIMIT 1"));
                                echo $locale['global_407'].showdate('shortdate', $data['user_actiontime']);
                                echo $locale['global_408']."<br /><br />".$data['suspend_reason'];
                                break;
                            case 4:
                                echo $locale['global_409'];
                                break;
                            case 5:
                                echo $locale['global_411'];
                                break;
                            case 6:
                                echo $locale['global_412'];
                                break;
                        }
                    }
                    break;
            }
            echo "</div>\n";
        }

        echo openform('crossover_login', 'post', ['max_tokens' => 1]);
        echo form_text('user_name', $locale['sh_0103'], $_POST['user_name'], ['class' => 'm-b-0', 'placeholder' => $locale['global_101']]);
        echo form_text('user_pass', $locale['sh_0104'], $_POST['user_pass'], ['class' => 'm-b-10', 'placeholder' => $locale['global_102'], 'type' => 'password']);
        echo "<input type='checkbox' name='remember_me' value='y' /> ".$locale['global_103']."</label>\n<br/>";
        echo form_button('login', $locale['global_104'], $locale['global_104'], ['class' => 'btn-primary m-t-20 m-b-20']);
        echo closeform();

        if ($settings['enable_registration']) {
            echo "<p>".$locale['global_105']."</p>\n";
        }
        echo $locale['global_106'];
        echo "</div></div>\n";
    }
    closetable();
}
