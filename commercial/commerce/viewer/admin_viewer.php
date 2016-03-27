<?php

namespace commerce\viewer;

class admin_viewer extends \commerce\model\admin_resource {

    public function dashboard() {
        global $userdata;

        ?>
        <section class="store_admin">

            <div class="header">
                <h3 class="display-inline-block">Today's</h3>
                <div class="display-inline-block">Total</div>
            </div>

            @ To replace a data analytics chart here

            <?php
            /*
            foreach ($_lastyear as $_query) {
                $_lastyear_posts[] = dbcount("('post_id')", DB_FORUM_POSTS, "post_datestamp BETWEEN $_query");
            }

            $barchart[1]['label'] = date('Y', strtotime('last year'));
            $barchart[1]['fillColor'] = 'rgba(213,216,218,1)';
            $barchart[1]['strokeColor'] = 'rgba(213,216,218,0)';
            $barchart[1]['highlightFill'] = 'rgba(213,216,218,1)';
            $barchart[1]['data'] = "[" . implode(',', $_lastyear_posts) . "]";

            foreach ($_thisyear as $_query) {
                $_thisyear_posts[] = dbcount("('post_id')", DB_FORUM_POSTS, "post_datestamp BETWEEN $_query");
            }

            $barchart[2]['label'] = date('Y', strtotime('this year'));
            $barchart[2]['fillColor'] = 'rgba(38,183,151,0.5)';
            $barchart[2]['strokeColor'] = 'rgb(38,183,151)';
            $barchart[2]['highlightFill'] = 'rgb(38,183,151)';
            $barchart[2]['data'] = "[" . implode(',', $_thisyear_posts) . "]";

            $label = array(
                'Jan', 'Feb', 'March', 'April', 'May', 'June', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec',
            );
            openside("Global Forum Post Statistics");
            echo "<div class='row'>\n<div class='col-xs-12 col-sm-8 col-md-8 col-lg-9'>\n";
            echo chart_line('mainchart', '', $label, $barchart, array('width' => '900', 'height' => '250'));

            echo "</div>\n<div class='col-xs-12 col-sm-4 col-md-4 col-lg-3'>\n";
            echo progress_bar($forum['thread'] / ($forum['count'] > 0 ? $forum['count'] : 1), "<span class='text-smaller text-dark text-uppercase'>Threads per Forum</span>", '', '5px');
            echo progress_bar($forum['thread'] / ($forum['users'] > 0 ? $forum['users'] : 1), "<span class='text-smaller text-dark text-uppercase'>Threads per User</span>", '', '5px');
            echo progress_bar($forum['post'] / ($forum['thread'] > 0 ? $forum['thread'] : 1), "<span class='text-smaller text-dark text-uppercase'>Forum Answer Rate</span>", '', '5px');
            echo progress_bar($forum['users'] / ($members['registered'] > 0 ? $members['registered'] : 1), "<span class='text-smaller text-dark text-uppercase'>Forum Usage Rate</span>", '', '5px');
            echo "</div>\n</div>\n";
            closeside();
            unset($barchart);
            */
            ?>
            <div class="row m-b-20">
                <div class="col-xs-12 col-sm-3">

                    <div class="list-group-item">
                        <h1 class="display-inline-block m-r-10">654 / 30</h1>
                        <div class="display-inline-block">Downloads / Purchases</div>
                    </div>

                </div>
                <div class="col-xs-12 col-sm-3">
                    <div class="list-group-item">
                        <h1 class="display-inline-block m-r-10">20</h1>
                        <div class="display-inline-block">New Submissions</div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-3">
                    <div class="list-group-item">
                        <h1 class="display-inline-block m-r-10">654</h1>
                        <div class="display-inline-block">Views</div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-3">
                    <div class="list-group-item">
                        <h1 class="display-inline-block m-r-10">654 / 6</h1>
                        <div class="display-inline-block">Comments & Ratings</div>
                    </div>

                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <h3>Recent Orders</h3>
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>Orders#</th>
                            <th>Payment Status</th>
                            <th>Shipping Status</th>
                            <th>User</th>
                            <th>Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><a href="">#0008</a><br/><?php echo showdate("longdate", time()) ?></td>
                            <td><span class="badge">Paid</span></td>
                            <td><span class="badge">Downloaded</span></td>
                            <td>
                                <div class="pull-left m-r-10"><?php echo display_avatar($userdata, "35px", "", TRUE, "") ?></div>
                                <div class="overflow-hide">
                                    <?php echo profile_link($userdata['user_id'], $userdata['user_name'], $userdata['user_status']) ?>
                                    <br/>
                                    <?php if (isset($userdata['user_firstname']) && isset($userdata['user_middlename']) && isset($userdata['user_lastname'])) : ?>
                                    <?php echo $userdata['user_firstname']." ".$userdata['user_middlename']." ".$userdata['user_lastname'] ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                $ 239.00
                            </td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="3">pagenav</td>
                            <td colspan="2" class="text-right">list how many per page</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <h3>Download Analytics</h3>
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>User</th>
                            <th>Downloaded Times</th>
                            <th>Amount Expended</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <div class="pull-left m-r-10"><?php echo display_avatar($userdata, "35px", "", TRUE, "") ?></div>
                                <div class="overflow-hide">
                                    <?php echo profile_link($userdata['user_id'], $userdata['user_name'], $userdata['user_status']) ?>
                                    <br/>
                                    <?php if (isset($userdata['user_firstname']) && isset($userdata['user_middlename']) && isset($userdata['user_lastname'])) : ?>
                                    <?php echo $userdata['user_firstname']." ".$userdata['user_middlename']." ".$userdata['user_lastname'] ?>
                                    <?php endif;?>
                                </div>
                            </td>
                            <td>35</td>
                            <td>0.00</td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="3">pagenav</td>
                            <td colspan="2" class="text-right">list how many per page</td>
                        </tr>
                        </tfoot>
                    </table>

                </div>

            </div>
        </section>
        <?php
    }

    /**
     * Catalog tab viewer
     */
    public function catalog() {

        if (!isset($_GET['refs'])) {
            $_GET['refs'] = "product";
        }

        $catalog_pages = $this->get_catalog_pages();

        ?>
        <div class="row">
            <div class="col-xs-12 col-sm-2">

                <ul class="list-group">
                    <?php
                    $current_function = "";

                    foreach($catalog_pages as $key => $value) {

                        if ($key == "module") { // module is array

                            foreach($value as $module_folder => $module_data) {

                                $active = isset($_GET['refs']) && $_GET['refs'] == $module_folder ? TRUE : FALSE;

                                $page_title = $active ? "<strong>".$module_data['title']."</strong>" : $module_data['title'];

                                $current_function = $active ? $module_data['function'] : $current_function;

                                echo '<li class="list-group-item '.($active == true ? "active" : "").'">';

                                echo '<a href="'.clean_request("refs=$module_folder", array("section", "aid"), true).'">';

                                echo $page_title;

                                echo '</a>';

                                echo '</li>';
                            }

                        } else {

                            $active = isset($_GET['refs']) && $_GET['refs'] == $key ? TRUE : FALSE;

                            $page_title = $active ? "<strong>".$value['title']."</strong>" : $value['title'];

                            $current_function = $active ? $value['function'] : $current_function;

                            echo '<li class="list-group-item '.($active == true ? "active" : "").'">';

                            echo '<a href="'.clean_request("refs=$key", array("section", "aid"), true).'">';

                            echo $page_title;

                            echo '</a>';

                            echo '</li>';

                        }
                    }
                    ?>
                </ul>
            </div>
            <div class="col-xs-12 col-sm-10">
                <?php

                parent::catalog_resource($current_function)->administration();

                ?>
            </div>
        </div>
        <?php
    }


    /**
     * Module administration page
     */
    public function modules() {

        $modules = $this->get_all_modules();

        $locale = $this->get_locale();

        $locale['fmt_module'] = "module|modules";

        if (isset($_POST['install'])) {
            $this->install_module($_POST['install']);
        } elseif (isset($_POST['uninstall'])) {
            $this->uninstall_module($_POST['uninstall']);
        }

        ?>
        <h3 class="m-t-20 m-b-0"><?php echo format_word(count($modules), $locale['fmt_module']) ?></h3>

        <div class="list-group">

        <?php
        if (!empty($modules) && is_array($modules)) {

            echo openform("module_form", "POST", FUSION_REQUEST);

            foreach ($modules as $i => $module) {

                ?>
                <div class="list-group-item">
                    <table>
                        <tr>
                            <td rowspan="2" class="col-xs-1 align-top p-r-20">
                                <img alt="<?php echo $module['name'] ?>" src='<?php echo COMMERCE_MODULES . $module['folder']."/".$module['icon'] ?>'/>
                            </td>
                            <td class="align-top" style="width:350px;">
                                <h3><?php echo $module['name'] ?></h3>
                                <div class="m-b-20">
                                    <?php echo ($module['status'] > 0 ? "<span class='badge align-top'>Enabled</span>" : "<span class='badge'>Disabled</span>"); ?>
                                </div>

                            </td>
                            <td class="align-left align-top">
                                <?php echo nl2br($module['description']) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="display-block m-b-20">Version <?php echo ($module['version'] ? $module['version'] : '1.00') ?></div>
                                <?php
                                if ($module['status'] > 0) {
                                    if ($module['status'] > 1) {
                                        echo form_button('install', "Upgrade", $module['folder'], array('class' => 'btn-primary'));
                                    } else {
                                        echo form_button('uninstall', "Uninstall", $module['folder'], array('class' => 'btn-default'));
                                    }
                                } else {
                                    echo form_button('install', "Install", $module['folder'], array('class' => 'btn-primary'));
                                }
                                ?>
                            </td>
                            <td>
                                <?php echo ($module['email'] ? "Email: <a href='mailto:".$module['email']."'>Support Email</a>" : ''); ?><br/>
                                <?php echo ($module['url'] ? "Website: <a href='".$module['url']."' target='_blank'>" : "")." ".($module['developer'] ? $module['developer'] : $locale['410'])." ".($module['url'] ? "</a>" : "") ?><br/>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php
            }

            echo closeform();

        } else { ?>

            <div class='list-group-item text-center'>There are no modules found.</div>

        <?php }
        ?>
        </div>
        <?php

    }

    public function settings() {

    }

}