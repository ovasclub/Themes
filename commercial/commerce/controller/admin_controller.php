<?php

namespace commerce\controller;

use commerce\viewer\admin_viewer;

class admin_controller {


    function __construct() {

        $tab_title['title'][] = "Dashboard";
        $tab_title['id'][] = "dashboard";

        $tab_title['title'][] = "Catalog";
        $tab_title['id'][] = "catalog";

        $tab_title['title'][] = "Promotions";
        $tab_title['id'][] = "promotions";

        $tab_title['title'][] = "Modules";
        $tab_title['id'][] = "modules";

        $tab_title['title'][] = "Store Setup";
        $tab_title['id'][] = "settings"; // modules appear here

        $control_section = array(
            "dashboard" => "dashboard",
            "catalog" => "catalog",
            "promotions" => "promotions",
            "modules" => "modules",
            "settings" => "settings"
        );

        $section = isset($_GET['section']) && isset($control_section[$_GET['section']]) ? $_GET['section'] : "dashboard";

        opentable("");

        echo opentab($tab_title, $section, "", true);

        if (isset($control_section[$section])) {
            $this->display_admin_page($control_section[$section]);
        }

        echo closetab();
        closetable();
    }

    private function display_admin_page($function_args) {

        $admin_viewer = new admin_viewer();

        if (method_exists($admin_viewer, $function_args)) {

            return $admin_viewer->$function_args();
        }
        return null;
    }


}