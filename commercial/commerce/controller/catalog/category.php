<?php

/**
 * Catalog/Category Administration Page
 */

namespace commerce\controller\catalog;

use commerce\model\admin_resource;

class category extends admin_resource {

    private $input_value = array(
        "cid" => 0,
        "title" => "",
        "description" => "",
        "access" => "",
        "image" => "",
        "thumbnail" => "",
        "parentid" => "",
        "status" => 1,
        "language" => LANGUAGE,
        "ordernum" => 0,
        "orderby" => 0,
    );

    private $module;
    public function __construct() {

        parent::__construct();


        if (!empty(self::$catalog_pages['module'])) {

            foreach(self::$catalog_pages['module'] as $module_name => $moduleData) {

                $category_mod_file = COMMERCE_MODULES.$module_name."/category.php";

                $class_name = self::$catalog_pages['module'][$module_name]['callback'];

                $namespace_class = "module".DIRECTORY_SEPARATOR."$module_name".DIRECTORY_SEPARATOR.$class_name;

                if (file_exists($category_mod_file)) {

                    require_once $category_mod_file;

                    if (class_exists($namespace_class)) {
                        // many namespace class
                        $class =  new \ReflectionClass($namespace_class);
                        $this->module[$module_name] = $class->newInstance();
                    }

                }
            }
        }
    }


    /**
     * Administration function controller
     */
    public function administration() {

        // list and new together..

        $tab_title['title'][] = "All Categories";
        $tab_title['id'][] = "cat_list";

        $tab_title['title'][] = isset($_GET['action']) ? "Edit Category" : "Add New Category";
        $tab_title['id'][] = "add_cat";

        $tab_active = tab_active($tab_title, isset($_GET['action']) ? 1 : 0);

        echo opentab($tab_title, $tab_active, "app_cat", FALSE, "m-t-20");

        echo opentabbody($tab_title['title'][0], $tab_title['id'][0], $tab_active);
        $this->list_category();
        echo closetabbody();

        echo opentabbody($tab_title['title'][1], $tab_title['id'][1], $tab_active);
        $this->category_form();
        echo closetabbody();

        echo closetab();
    }

    /**
     * Category Form for Edit or New Category
     * @todo: Need to do delete thumbnail
     */
    private function category_form() {

        $locale = $this->get_locale();

        $exit_link = clean_request("", array("aid", "section", "refs"), TRUE);

        $cat_hidden = array();

        if (isset($_GET['action']) && isset($_GET['cid'])) {
            if (isnum($_GET['cid'])) {
                switch($_GET['action']) {
                    case "edit":
                        $result = dbquery("SELECT * FROM ".DB_STORE_CATS." WHERE cid='".intval($_GET['cid'])."'");
                        if (dbrows($result)>0) {
                            $this->input_value = dbarray($result);
                            $cat_hidden = array($_GET['cid']);
                        } else {
                            redirect($exit_link);
                        }
                        break;
                    case "del":
                        if (dbcount("(cid)", DB_STORE_CATS, "cid='".intval($_GET['cid'])."'")) {
                            dbquery("DELETE FROM ".DB_STORE_CATS." WHERE cid='".intval($_GET['cid'])."'");
                            addNotice("success", "Category deleted successfully");
                        } else {
                            addNotice("danger", "Category was not deleted due to error");
                        }
                        redirect($exit_link);
                        break;
                    case "enable":
                        if (dbcount("(cid)", DB_STORE_CATS, "cid='".intval($_GET['cid'])."'")) {
                            dbquery("UPDATE ".DB_STORE_CATS." SET status='1' WHERE cid='".intval($_GET['cid'])."'");
                            addNotice("success", "Category enabled");
                        } else {
                            addNotice("danger", "Category was not updated due to error");
                        }
                        redirect($exit_link);
                        break;
                    case "disable":
                        if (dbcount("(cid)", DB_STORE_CATS, "cid='".intval($_GET['cid'])."'")) {
                            dbquery("UPDATE ".DB_STORE_CATS." SET status='0' WHERE cid='".intval($_GET['cid'])."'");
                            addNotice("success", "Category disabled");
                        } else {
                            addNotice("danger", "Category was not updated due to error");
                        }
                        redirect($exit_link);
                        break;
                    default:
                        redirect($exit_link);
                }
            } else {
                redirect($exit_link);
            }
        }


        if (isset($_POST['save_category'])) {

            $this->input_value = array(
                "cid" => form_sanitizer($_POST['cid'], 0, "cid"),
                "parentid" => form_sanitizer($_POST['title'], 0, "title"),
                "title" => form_sanitizer($_POST['title'], "", "title"),
                "description" => form_sanitizer($_POST['description'], "", "description"),
                "access" => form_sanitizer($_POST['access'], USER_LEVEL_PUBLIC, "access"),
                "language" => form_sanitizer($_POST['language'], LANGUAGE, "language"),
                "image" => isset($_POST['image']) ? form_sanitizer($_POST['image'], "", "image") : "",
                "image_thumb" => isset($_POST['image_thumb']) ? form_sanitizer($_POST['image_thumb'], "", "image_thumb") : "",
                "status" => form_sanitizer($_POST['status'], 0, "status"),
                "ordernum" => form_sanitizer($_POST['ordernum'], 0, "ordernum"),
                "orderby" => form_sanitizer($_POST['orderby'], 0, "orderby"),
            );

            if (empty($this->input_value['ordernum'])) {
                $this->input_value['ordernum'] = dbresult(dbquery("SELECT MAX(ordernum) FROM ".DB_STORE_CATS."
				where parentid='".$this->input_value['parentid']."'"), 0)+1;
            }

            if (!empty($this->module)) {
                foreach($this->module as $module_name => $module_class) {
                    if (method_exists($this->module[$module_name], "get_form_input")) {
                        $this->input_value += $this->module[$module_name]->get_form_input($this->input_value);
                    }
                }
            }


            if (\defender::safe()) {
                if ($this->store_settings['allow_cat_img']) {
                    if (!empty($_FILES['image']['name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
                        $upload = form_sanitizer($_FILES['image'], '', 'image');
                        if ($upload['error'] == 0) {
                            $this->input_value['image'] = $upload['image'];
                            $this->input_value['image_thumb'] = $upload['thumb1_name'];
                            unset($upload);
                        }
                    }
                }
            }

            if (\defender::safe()) {
                if (!empty($this->input_value['cid']) && dbcount("(cid)", DB_STORE_CATS, "cid='".intval($this->input_value['cid'])."'")
                ) {
                    dbquery_order(DB_STORE_CATS, $this->input_value['ordernum'], 'ordernum',
                                  $this->input_value['cid'], 'cid',
                                  $this->input_value['parentid'], 'parentid', TRUE, 'language', 'update');

                    dbquery_insert(DB_STORE_CATS, $this->input_value, "update");

                    addNotice("success", "Store category updated");

                } else {

                    dbquery_order(DB_STORE_CATS, $this->input_value['ordernum'], 'ordernum',
                                  0, "cid", $this->input_value['parentid'], 'parentid', TRUE, 'language', 'save');

                    dbquery_insert(DB_STORE_CATS, $this->input_value, "save");

                    addNotice("success", "Store category added");
                }

                redirect($exit_link);
            }
        }

        echo openform("app_cat_form", "post", FUSION_REQUEST, array("class"=>"m-t-20", "enctype" => true));
        ?>
        <div class="row">
            <div class="col-xs-12 col-sm-8">
                <?php
                echo form_hidden("cid", "", $this->input_value['cid']);

                if (!empty($this->module)) {
                    foreach($this->module as $module_name => $module_class) {
                        if (method_exists($this->module[$module_name], "display_form_input")) {
                            echo $this->module[$module_name]->display_form_input($this->input_value, "top");
                        }
                    }
                }

                echo form_text("title", "Category Title", $this->input_value['title'], array("inline"=>TRUE, "required"=>TRUE));

                if ($this->commerce_settings['allow_cat_img']) {
                    echo form_fileinput("image", "Category Image", $this->input_value['image'],
                                        array(
                                            "upload_path" => STORE."ct_img/",
                                            "max_width" => $this->commerce_settings['category_image_w'],
                                            "max_height" => $this->commerce_settings['category_image_h'],
                                            "max_byte" => $this->commerce_settings['category_image_b'],
                                            "thumbnail" => TRUE,
                                            "thumbnail_w" => $this->commerce_settings['category_image_thumb_w'],
                                            "thumbnail_h" => $this->commerce_settings['category_image_thumb_h'],
                                            "thumbnail2" => FALSE,
                                            "template" => "thumbnail",
                                        )
                    );
                }

                echo form_textarea("description", "Description", $this->input_value['description'], array(
                    "type" => "tinymce",
                    "tinymce" => "simple",
                    "inline" => TRUE,
                ));

                if (!empty($this->module)) {
                    foreach($this->module as $module_name => $module_class) {
                        if (method_exists($this->module[$module_name], "display_form_input")) {
                            echo $this->module[$module_name]->display_form_input($this->input_value, "middle");
                        }
                    }
                }

                echo form_btngroup("status", "Status", $this->input_value['status'],
                               array("options"=> array("0"=>$locale['disable'], "1"=>$locale['enable']),
                                     "inline"=>TRUE)
                );

                echo form_select_tree("parentid", "Parent", $this->input_value['parentid'], array(
                    "disable_opts" => $cat_hidden,
                    "hide_disabled" => TRUE,
                    "inline" => TRUE,
                ), DB_STORE_CATS, "title", "cid", "parentid");

                echo form_select("access", "Access", $this->input_value['access'],
                                 array("options"=> fusion_get_groups(), "inline"=>TRUE,
                                 ));


                echo form_select("orderby", "Order Products By", $this->input_value['orderby'],
                                 array(
                                     "options" => $this->product_order_filter,
                                     "inline" => TRUE,
                                 )
                );

                echo form_text("ordernum", "Category Order", $this->input_value['ordernum'],
                               array("type"=> "number", "inline"=>TRUE, "width" => "200px",
                               ));

                if (!empty($this->module)) {
                    foreach($this->module as $module_name => $module_class) {
                        if (method_exists($this->module[$module_name], "display_form_input")) {
                            echo $this->module[$module_name]->display_form_input($this->input_value, "bottom");
                        }
                    }
                }

                ?>
            </div>
            <div class="col-xs-12 col-sm-4">
                <?php

                if (multilang_table("cms")) {

                    $languages = !empty($this->input_value['language']) ? explode('.', $this->input_value['language']) : array();

                    echo "<div class='row'>\n";
                    echo "<label class='col-xs-12 col-sm-3'>Languages</label>\n";
                    echo "<div class='col-xs-12 col-sm-9'>\n";

                    foreach (fusion_get_enabled_languages() as $language => $language_name) {

                        $isDisabled = fusion_get_settings("locale") == $language ? TRUE : FALSE;

                        echo form_checkbox('language[]', $language_name, in_array($language, $languages) ? 1 : 0, array(
                            'class' => 'm-b-0',
                            'value' => $language,
                            'input_id' => 'lang-'.$language,
                            'reverse_label'=>TRUE,
                            "disabled" => $isDisabled ? TRUE : FALSE,
                        ));

                        if ($isDisabled) {
                            echo form_hidden("language[]", "", $language);
                        }
                    }
                    echo "</div>\n";
                    echo "</div>\n";

                } else {
                    echo form_hidden('language', '', $this->input_value['page_language']);
                }
                ?>
            </div>
        </div>
        <?php
        echo form_button("save_category", $locale['save_changes'], $locale['save'], array("class"=>"btn-primary"));
        echo closeform();
    }

    private function category_submissions() {

    }

    /**
     * Category listing function
     */
    private function list_category() {

        $locale = $this->get_locale();

        if (!isset($_GET['cat_id'])) {
            $_GET['cat_id'] = 0;
        }

        $data = $this->get_category();

        if (!empty($data[$_GET['cat_id']])) : ?>

            <?php
            // join in other information
            $cid_to_sql = implode(",", array_keys($data[$_GET['cat_id']]));
            // Omit language and access alike since this is a safe list
            // come back to this after done with addondb form
            $result = dbquery("SELECT c.cid, count(a.id) 'product_count'
            FROM ".DB_STORE_CATS." c
            LEFT JOIN ".DB_STORE_PRODUCTS." a using (cid)
            WHERE cid IN ($cid_to_sql)");

            ?>

            <table class="table m-t-20 table-striped table-hover">
            <thead>
            <tr>
                <th class="col-xs-1"></th>
                <th class="col-xs-4">Category</th>
                <th>Featured</th>
                <th>Subcategory</th>
                <th>Apps</th>
                <th></th>
            </tr>
            </thead>
            <tbody>

            <?php foreach($data[$_GET['cat_id']] as $cat_id => $cdata) :

            echo "<tr>\n";

                $preserved_array = array("section", "refs", "view", "aid");

                if ($cdata['status'] == 1) {
                    $button_link = "<a href='".clean_request("action=disable&cid=".$cat_id, $preserved_array)."'>Disable</a>";
                } else {
                    $button_link = "<a href='".clean_request("action=enable&cid=".$cat_id, $preserved_array)."'>Enable</a>";
                }

                $browse_link = clean_request("cat_id=".$cat_id, $preserved_array , TRUE);
                $edit_link = clean_request("action=edit&cid=".$cat_id, $preserved_array, TRUE);
                $delete_link = clean_request("action=delete&cid=".$cat_id, $preserved_array, TRUE);

                echo "<td>".$button_link."</td>\n";
                echo "<td><a href='$browse_link'>".$cdata['title']."</a></td>\n";
                echo "<td>xx</td>\n";
                echo "<td>xxx</td>\n";
                echo "<td>xxx</td>\n";
                echo "
                    <td>\n
                        <a href='$edit_link'>".$locale['edit']."</a>\n - <a href='$delete_link'>".$locale['delete']."</a>\n
                    </td>\n";
            echo "</tr>";

            endforeach; ?>
            </tbody>
            </table>
        <?php else: ?>
            <div class="well text-center m-t-20 m-b-20">There are no categories defined. Please add a category</div>
        <?php endif; ?>
        <?php
    }
}