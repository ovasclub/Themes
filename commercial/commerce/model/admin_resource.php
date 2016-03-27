<?php

/**
 * Class StoreResource
 * Defines the entire data model for Commerce
 */
namespace commerce\model;

use commerce\controller\catalog\category;
use commerce\controller\catalog\product;
use commerce\controller\catalog\reviews;
use commerce\controller\catalog\settings;


abstract class admin_resource {

    protected $settings = array();

    protected $commerce_settings = array();

    protected static $category = array();

    protected static $category_index = array();

    protected static $installed_modules;

    protected $product_order_filter = array();


    public function __construct() {

        $this->settings = fusion_get_settings();

        $this->commerce_settings = get_settings("cms");

        $this->product_order_filter = array(
            "product_title ASC" => "Product Name ASC",
            "product_title DESC" => "Product Name DESC",
            "product_id ASC" => "Product ID ASC",
            "product_id DESC" => "Product ID DESC",
            "product_price ASC" => "Product Price ASC",
            "product_price DESC" => "Product Price DESC",
        );

        if (empty(self::$category)) {
            self::$category = dbquery_tree_full(
                DB_STORE_CATS,
                "cid",
                "parentid",
                "WHERE ".groupaccess('access')." ".(multilang_table("SHOP") ? "AND ".in_group("language", LANGUAGE) : "")." ORDER BY ordernum ASC");
        }

        if (empty(self::$category_index)) {
            self::$category_index = dbquery_tree(
                DB_STORE_CATS,
                "cid",
                "parentid",
                "WHERE ".groupaccess('access')." ".(multilang_table("SHOP") ? "AND ".in_group("language", LANGUAGE) : "")." ORDER BY ordernum ASC");
        }

        // All installed modules
        if (empty(self::$installed_modules)) {
            $result = dbquery("SELECT * FROM ".DB_STORE_MODULES);
            if (dbrows($result)>0) {
                while ($modData = dbarray($result)) {
                    self::$installed_modules = $modData;
                }
            }
        }

    }

    protected function get_userdata($key = NULL) {
        global $userdata;
        return $key === NULL ? $userdata : (isset($userdata[$key]) ? $userdata[$key] : NULL);

    }


    /** Fetches Store Category */
    protected function get_category($cid = NULL, $index = NULL) {

        if ($index !== NULL && !empty(self::$category_index)) {

            if ($cid !== NULL) {
                $category_index = flatten_array(self::$category_index);
                return (isset($category_index[$cid]) ? $category_index[$cid] : self::$category_index);
            } else {
                return self::$category_index;
            }

        } else {

            if ($cid !== NULL && !empty(self::$category)) {
                $category = flatten_array(self::$category);
                return (isset($category[$cid]) ? $category[$cid] : self::$category);
            } else {
                return self::$category;
            }

        }

    }

    protected function get_locale() {
        global $locale;
        static $loc = array();
        if (empty($loc) && is_array($locale)) {
            foreach($locale as $key => $value) {
                $loc[$key] = strip_tags($value);
            }
        }
        return $loc;
    }


    /**
     * Catalog data model including modules loaded
     */
    protected static $catalog_pages = array();

    protected function get_catalog_pages() {

        if (empty($catalog_pages)) {

            self::$catalog_pages = array(
                "product" => array("title" => "Product", "function" => "product"),
                "category" => array("title" => "Category", "function" => "category"),
                "reviews" => array("title" => "Reviews", "function" => "reviews"),
                "settings" => array("title" => "Front Page Settings", "function" => "settings"),
            );

            // load all the module pages here

            $result = dbquery("SELECT * FROM ".DB_STORE_MODULES);
            if (dbrows($result)>0) {
                while ($data = dbarray($result)) {
                    self::$catalog_pages["module"][$data['module_folder']] = array(
                        "title"=>$data['module_title'],
                        "function"=> $data['module_folder'],
                        "callback"=> $data['module_callback']
                    );
                }
            }

        }
        return self::$catalog_pages;
    }


    /**
     * Global run-time model for commerce administration interface
     * @param $interface
     * @return category|product|reviews|settings
     * @throws \Exception
     */
    protected function catalog_resource($interface) {

        switch($interface) {
            case "category":
                return new category();
            break;
            case "product":
                return new product();
            break;
            case "reviews":
                return new reviews();
            break;
            case "settings":
                return new settings();
            default:

                if (isset(self::$catalog_pages['module'][$interface]['callback']) && file_exists(COMMERCE_MODULES. $interface ."/catalog.php")) {

                    require_once COMMERCE_MODULES.$interface."/catalog.php";

                    $class_name = self::$catalog_pages['module'][$interface]['callback'];

                    $namespace_class = "module".DIRECTORY_SEPARATOR."$interface".DIRECTORY_SEPARATOR.$class_name;

                    if (class_exists($namespace_class)) {

                        return new $namespace_class;

                    } elseif (class_exists($class_name)) {

                        return new $class_name;

                    }

                }

                throw new \Exception("No class specified or class is not a valid class.");
        }
    }


    protected function load_catalog_modules($module_name) {

        if (isset(self::$catalog_pages['module'][$module_name]['callback']) && file_exists(COMMERCE_MODULES. $module_name ."/catalog.php")) {

            require_once COMMERCE_MODULES.$module_name."/catalog.php";

            $class_name = self::$catalog_pages['module'][$module_name]['callback'];

            $namespace_class = "module".DIRECTORY_SEPARATOR."$module_name".DIRECTORY_SEPARATOR.$class_name;

            if (class_exists($namespace_class)) {

                return new $namespace_class;

            } elseif (class_exists($class_name)) {

                return new $class_name;

            }

        }

    }





    protected static $modules;

    /**
     * @param string $folder
     * @return array
     */
    protected function load_modules($folder) {

        $module = array();

        $module_title = "";
        $module_callback = "";
        $module_description = "";
        $module_version = "";
        $module_developer = "";
        $module_email = "";
        $module_weburl = "";
        $module_folder = "";
        $module_newtable = array();
        $module_insertdbrow = array();
        $module_droptable = array();
        $module_altertable = array();
        $module_deldbrow = array();
        $module_sitelink = array();
        $module_adminpanel = array();
        $module_mlt = array();
        $mlt_insertdbrow = array();
        $mlt_deldbrow = array();
        $module_delfiles = array();
        $module_newcol = array();
        $module_dropcol = array();
        $module_icon = "";

        if (is_dir(COMMERCE_MODULES . $folder) && file_exists(COMMERCE_MODULES . $folder."/module.php")) {

            include COMMERCE_MODULES . $folder."/module.php";

            $module = array(
                'name' => str_replace('_', ' ', $module_title),
                'title' => $module_title,
                'callback' => $module_callback,
                'description' => $module_description,
                'version' => $module_version ? : 'beta',
                'developer' => $module_developer ? : 'PHP-Fusion',
                'email' => $module_email,
                'url' => $module_weburl,
                'folder' => $module_folder,
                'newtable' => $module_newtable,
                'newcol' => $module_newcol,
                'dropcol' => $module_dropcol,
                'insertdbrow' => $module_insertdbrow,
                'droptable' => $module_droptable,
                'altertable' => $module_altertable,
                'deldbrow' => $module_deldbrow,
                'sitelink' => $module_sitelink,
                'adminpanel' => $module_adminpanel,
                'mlt' => $module_mlt,
                'mlt_insertdbrow' => $mlt_insertdbrow,
                'mlt_deldbrow' => $mlt_deldbrow,
                'delfiles' => $module_delfiles,
                "icon" => $module_icon,
            );
            $result = dbquery("SELECT module_version FROM ".DB_STORE_MODULES." WHERE module_folder=:module_folder", array(':module_folder' => $folder));
            $module['status'] = dbrows($result)
                ? (version_compare($module['version'], dbresult($result, 0), ">")
                    ? 2
                    : 1)
                :  0;
        }
        return $module;

    }

    protected function get_all_modules() {

        if (empty(self::$modules)) {
            $temp = opendir(COMMERCE_MODULES);
            while ($folder = readdir($temp)) {
                if (!in_array($folder, array("..", ".")) && ($module = $this->load_modules($folder))) {
                    self::$modules[] = $module;
                }
            }
            closedir($temp);
        }

        return self::$modules;
    }


    /**
     * Install the module
     * @param $modules_folder
     */
    protected function install_module($folder) {

        $error = "";
        if ( ($module = $this->load_modules($folder)) ) {

            $result = dbquery("SELECT module_id, module_version FROM ".DB_STORE_MODULES." WHERE module_folder=:folder", array(':folder' => $folder));

            if (dbrows($result)) {

                $data = dbarray($result);

                if ($module['version'] > $data['module_version']) {
                    if ($module['altertable'] && is_array($module['altertable'])) {
                        foreach ($module['altertable'] as $alter) {
                            $result = dbquery("ALTER TABLE ".$alter);
                        }
                    }
                    dbquery("UPDATE ".DB_STORE_MODULES." SET module_version=:version WHERE module_id=:id", array(
                        ':version' => $module['version'],
                        ':id' => $module['id'],
                    ));
                }

            } else {

                if ($module['adminpanel'] && is_array($module['adminpanel'])) {
                    $error = 0;
                    foreach ($module['adminpanel'] as $adminpanel) {

                        // auto recovery
                        if (!empty($adminpanel['rights'])) {
                            dbquery("DELETE FROM ".DB_ADMIN." WHERE admin_rights='".$adminpanel['rights']."'");
                        }

                        $module_admin_image = INFUSIONS.$module['folder'].($adminpanel['image'] ?: "infusion_panel.png");

                        if (empty($adminpanel['page'])) {
                            $item_page = 5;
                        } else {
                            $item_page = isnum($adminpanel['page']) ? $adminpanel['page'] : 5;
                        }

                        if (!dbcount("(admin_id)", DB_ADMIN, "admin_rights='".$adminpanel['rights']."'")) {
                            $adminpanel += array(
                                "rights" => "",
                                "title" => "",
                                "panel" => "",
                            );
                            dbquery("INSERT INTO ".DB_ADMIN." (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('".$adminpanel['rights']."', '".$module_admin_image."', '".$adminpanel['title']."', '".INFUSIONS.$module['folder']."/".$adminpanel['panel']."', '".$item_page."')");
                            $result = dbquery("SELECT user_id, user_rights FROM ".DB_USERS." WHERE user_level=".USER_LEVEL_SUPER_ADMIN);
                            while ($data = dbarray($result)) {
                                dbquery("UPDATE ".DB_USERS." SET user_rights='".$data['user_rights'].".".$adminpanel['rights']."' WHERE user_id='".$data['user_id']."'");
                            }
                        } else {
                            $error = 1;
                        }
                    }
                }
                if (!$error) {
                    if ($module['sitelink'] && is_array($module['sitelink'])) {
                        $last_id = 0;
                        foreach ($module['sitelink'] as $sitelink) {
                            $link_order = dbresult(dbquery("SELECT MAX(link_order) FROM ".DB_SITE_LINKS), 0)+1;
                            $sitelink += array(
                                "title" =>"",
                                "cat" => 0,
                                "url" => "",
                                "icon" => "",
                                "visibility" => 0,
                                "position" => 3,
                            );
                            if (!empty($sitelink['cat']) && $sitelink['cat'] == "{last_id}" && !empty($last_id)) {
                                $sitelink['cat'] = $last_id;
                                dbquery("INSERT INTO ".DB_SITE_LINKS." (link_name, link_cat, link_url, link_icon, link_visibility, link_position, link_window,link_language, link_order) VALUES ('".$sitelink['title']."', '".$sitelink['cat']."', '".str_replace("../", "", INFUSIONS).$module_folder."/".$sitelink['url']."', '".$sitelink['icon']."', '".$sitelink['visibility']."', '".$sitelink['position']."', '0', '".LANGUAGE."', '".$link_order."')");
                            } else {
                                dbquery("INSERT INTO ".DB_SITE_LINKS." (link_name, link_cat, link_url, link_icon, link_visibility, link_position, link_window,link_language, link_order) VALUES ('".$sitelink['title']."', '".$sitelink['cat']."', '".str_replace("../", "", INFUSIONS).$module_folder."/".$sitelink['url']."', '".$sitelink['icon']."', '".$sitelink['visibility']."', '".$sitelink['position']."', '0', '".LANGUAGE."', '".$link_order."')");
                                $last_id = dblastid();
                            }
                        }
                    }
                    //Multilang rights
                    if ($module['mlt'] && is_array($module['mlt'])) {
                        foreach ($module['mlt'] as $mlt) {
                            dbquery("INSERT INTO ".DB_LANGUAGE_TABLES." (mlt_rights, mlt_title, mlt_status) VALUES ('".$mlt['rights']."', '".$mlt['title']."', '1')");
                        }
                    }

                    if ($module['newtable'] && is_array($module['newtable'])) {
                        foreach ($module['newtable'] as $newtable) {
                            dbquery("CREATE TABLE IF NOT EXISTS ".$newtable);
                        }
                    }

                    // $newcol = array("table"=>DB_CATEGORY, "column"=>"some-col", "column_type"=>'varchar(200)');
                    if (isset($module['newcol']) && is_array($module['newcol'])) {
                        foreach ($module['newcol'] as $newCol) {
                            if (is_array($newCol) && !empty($newCol['table']) && !empty($newCol['column']) && !empty($newCol['column_type'])) {
                                $columns = fieldgenerator($newCol['table']);
                                $count = count($columns);
                                if (!in_array($newCol['column'], $columns)) {
                                    dbquery("ALTER TABLE ".$newCol['table']." ADD ".$newCol['column']." ".$newCol['column_type']." AFTER ".$columns[$count - 1]);
                                }
                            }
                        }
                    }

                    if ($module['insertdbrow'] && is_array($module['insertdbrow'])) {
                        $last_id = 0;
                        foreach ($module['insertdbrow'] as $insertdbrow) {
                            if (stristr($insertdbrow, "{last_id}") && !empty($last_id)) {
                                dbquery("INSERT INTO ".str_replace("{last_id}", $last_id, $insertdbrow));
                            } else {
                                dbquery("INSERT INTO ".$insertdbrow);
                                $last_id = dblastid();
                            }
                        }
                    }

                    if ($module['mlt_insertdbrow'] && is_array($module['mlt_insertdbrow'])) {
                        foreach (fusion_get_enabled_languages() as $current_language => $language_translations) {
                            if (isset($mlt_insertdbrow[$current_language])) {
                                $last_id = 0;
                                foreach($mlt_insertdbrow[$current_language] as $insertdbrow) {
                                    if (stristr($insertdbrow, "{last_id}") && !empty($last_id)) {
                                        dbquery("INSERT INTO ".str_replace("{last_id}", $last_id, $insertdbrow));
                                    } else {
                                        dbquery("INSERT INTO ".$insertdbrow);
                                        $last_id = dblastid();
                                    }
                                }
                            }
                        }
                    }

                    dbquery("INSERT INTO ".DB_STORE_MODULES." (module_title, module_callback, module_folder, module_version)
                    VALUES ('".$module['title']."', '".$module['callback']."', '".$module['folder']."', '".$module['version']."')");
                }
            }
        }
        addNotice("success", "Module ".$module['title']." installed");

        redirect(FUSION_REQUEST);

    }


    /**
     * Install the module
     * @param $modules_folder
     */
    protected function uninstall_module($folder) {

        $result = dbquery("SELECT module_folder FROM ".DB_STORE_MODULES." WHERE module_folder=:folder", array(':folder' => $folder));

        $data = dbarray($result);

        $module = $this->load_modules($folder);

        if ($module['adminpanel'] && is_array($module['adminpanel'])) {

            foreach ($module['adminpanel'] as $adminpanel) {

                dbquery("DELETE FROM ".DB_ADMIN." WHERE admin_rights='".($adminpanel['rights'] ? : "IP")."' AND admin_link='".INFUSIONS.$module['folder']."/".$adminpanel['panel']."' AND admin_page='5'");

                $result = dbquery("SELECT user_id, user_rights FROM ".DB_USERS." WHERE user_level<=".USER_LEVEL_ADMIN);

                while ($data = dbarray($result)) {
                    $user_rights = explode(".", $data['user_rights']);
                    if (in_array($adminpanel['rights'], $user_rights)) {
                        $key = array_search($adminpanel['rights'], $user_rights);
                        unset($user_rights[$key]);
                    }
                    dbquery("UPDATE ".DB_USERS." SET user_rights='".implode(".", $user_rights)."' WHERE user_id='".$data['user_id']."'");
                }

            }
        }

        if ($module['mlt'] && is_array($module['mlt'])) {
            foreach ($module['mlt'] as $mlt) {
                dbquery("DELETE FROM ".DB_LANGUAGE_TABLES." WHERE mlt_rights='".$mlt['rights']."'");
            }
        }

        if ($module['sitelink'] && is_array($module['sitelink'])) {
            foreach ($module['sitelink'] as $sitelink) {
                $result2 = dbquery("SELECT link_id, link_order FROM ".DB_SITE_LINKS." WHERE link_url='".str_replace("../", "", INFUSIONS).$module['folder']."/".$sitelink['url']."'");
                if (dbrows($result2)) {
                    $data2 = dbarray($result2);
                    dbquery("UPDATE ".DB_SITE_LINKS." SET link_order=link_order-1 WHERE link_order>'".$data2['link_order']."'");
                    dbquery("DELETE FROM ".DB_SITE_LINKS." WHERE link_id='".$data2['link_id']."'");
                }
            }
        }

        if (isset($module['deldbrow']) && is_array($module['deldbrow'])) {
            foreach ($module['deldbrow'] as $deldbrow) {
                dbquery("DELETE FROM ".$deldbrow);
            }
        }

        if ($module['mlt_deldbrow'] && is_array($module['mlt_deldbrow'])) {
            foreach(fusion_get_enabled_languages() as $current_language) {
                if (isset($module['mlt_deldbrow'][$current_language])) {
                    foreach($module['mlt_deldbrow'][$current_language] as $mlt_deldbrow) {
                        dbquery("DELETE FROM ".$mlt_deldbrow);
                    }
                }
            }
        }

        if (!empty($module['delfiles']) && is_array($module['delfiles'])) {
            foreach($module['delfiles'] as $folder) {
                $files = makefilelist($folder, ".|..|index.php", TRUE);
                if (!empty($files)) {
                    foreach($files as $filename) {
                        unlink($folder.$filename);
                    }
                }
            }
        }

        // array("table"=>DB_CATEGORY, "column"=>'category');
        if (isset($module['dropcol']) && is_array($module['dropcol'])) {
            foreach ($module['dropcol'] as $dropCol) {
                if (is_array($dropCol) && !empty($dropCol['table']) && !empty($dropCol['column'])) {
                    $columns = fieldgenerator($dropCol['table']);
                    if (in_array($dropCol['column'], $columns)) {
                        dbquery("ALTER TABLE ".$dropCol['table']." DROP COLUMN ".$dropCol['column']);
                    }
                }
            }
        }

        if ($module['droptable'] && is_array($module['droptable'])) {
            foreach ($module['droptable'] as $droptable) {
                dbquery("DROP TABLE IF EXISTS ".$droptable);
            }
        }
        dbquery("DELETE FROM ".DB_STORE_MODULES." WHERE module_folder=:folder", array(
            ':folder' => $folder
        ));

        addNotice("success", "Module ".$module['title']." uninstalled");
        redirect(FUSION_REQUEST);
    }






}
