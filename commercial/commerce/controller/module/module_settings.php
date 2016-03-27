<?php

namespace commerce\controller\module;

use commerce\model\admin_resource;

class module_settings extends admin_resource {

    private $modules;


    public function administration() {
        if (!isset($_GET['refs'])) {
            $_GET['refs'] = "product";
        }
        $catalog_pages = $this->get_catalog_pages();

        $modules = $this->get_all_modules();

        $content = "<table class='table table-responsive table-striped table-hover'>\n";
        $content .= "<thead>\n";
        $content .= "<tr>\n<th class='col-xs-1'></th><th>Module</th><th>Information</th></tr>\n";
        $content .= "</thead><tbody>\n";

        if ($modules) {
            $formaction = FUSION_SELF;
            foreach ($modules as $i => $inf) {
                $content .= "<tr>\n";
                $content .= "<td class='p-15'>\n";
                $content .= "<img src='".COMMERCE_MODULES . $inf['folder']."/".$inf['icon']."'/>\n";
                $content .= "</td>";
                $content .= "<td class='p-15'><h3 class='m-0 text-normal'>".$inf['name']."</h3>";
                $content .= "<small>Version ".($inf['version'] ? $inf['version'] : '')."</small>\n<br/>";
                $content .= "<div class='m-t-10'>\n";
                if ($inf['status'] > 0) {
                    if ($inf['status'] > 1) {
                        $content .= form_button('infuse', "Upgrade", $inf['folder'], array('class' => 'btn-primary infuse', 'icon' => 'entypo magnet'));
                    } else {
                        $content .= form_button('defuse', "Uninstall", $inf['folder'], array('class' => 'btn-info defuse', 'icon' => 'entypo trash'));
                    }
                } else {
                    $content .= form_button('infuse', "Install", $inf['folder'], array('class' => 'btn-primary infuse', 'icon' => 'entypo install'));
                }
                $content .= "".($inf['status'] > 0 ? "<div class='display-inline-block m-t-5 m-l-10'>Enabled</div>" : "<div class='display-inline-block m-t-5 m-l-10'>Disabled</div>");
                $content .= "</div>\n";
                $content .= "</td>\n";
                $content .= "<td class='p-15 col-xs-4'>".$inf['description']."<br/><br/>\n";
                $content .= ($inf['url'] ? "Website: <a href='".$inf['url']."' target='_blank'>" : "")." ".($inf['developer'] ? $inf['developer'] : $locale['410'])." ".($inf['url'] ? "</a>" : "")." <br/>".($inf['email'] ? "Email: <a href='mailto:".$inf['email']."'>Support Email</a>" : '')."</td>\n";
                $content .= "</tr>\n";
            }
        } else {
            $content .= "<tr>\n<td colspan='6'><div class='m-t-20 well text-center'>There are no modules found.</div></td>\n</tr>\n";
        }
        $content .= "</table>\n";


        // Design left col module access settings page once installed is made available
        ?>

        <div class="row">
            <div class="col-xs-12">
                <?php echo $content; ?>
            </div>
        </div>
        <?php
    }



}