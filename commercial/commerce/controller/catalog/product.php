<?php

namespace commerce\controller\catalog;

use commerce\model\admin_resource;

class product extends admin_resource {


    public function __construct() {

        parent::__construct();

        if (!empty(self::$catalog_pages['module'])) {

            foreach(self::$catalog_pages['module'] as $module_name => $moduleData) {

                $category_mod_file = COMMERCE_MODULES.$module_name."/product.php";

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



    // Model for Admin
    public function administration() {

        $category = $this->get_category();


        $tab_title['title'][] = "All Products";
        $tab_title['id'][] = "product_list";

        $tab_title['title'][] = isset($_GET['action']) ? "Edit Product" : "Add Product";
        $tab_title['id'][] = "product_form";


        $tab_active = tab_active($tab_title, isset($_GET['action']) ? 1 : 1);

        echo opentab($tab_title, $tab_active, "product_tab", FALSE, "m-t-20");

        echo opentabbody($tab_title['title'][0], $tab_title['id'][0], $tab_active);

        $this->list_product();

        echo closetabbody();

        echo opentabbody($tab_title['title'][1], $tab_title['id'][1], $tab_active);

        if (!empty($category)) {
            $this->product_form();
        } else {
            echo "<div class='well text-center m-t-20 m-b-20'>There are no category defined. Please add a category.</div>\n";
        }

        echo closetabbody();

        echo closetab();
    }

    private $input_value = array(
        "id" => 0,
        "cid" => 0,
        "parentid" => 0,
        "status" => 1,
        "title" => "",
        "intro" => "",
        "description" => "",
        "user_id" => 0,
        "user_ip" => USER_IP,
        "user_ip_type" => USER_IP_TYPE,
        "language" => LANGUAGE,
        "price" => "",
        "market_price" => "",
        "quantity" => "",
        "weight" => "",
        "shippable" => "",
        "free_shipping" => "",
        "access" => USER_LEVEL_PUBLIC,
        "datestamp" => 0,
        "allow_comments" => 1,
        "allow_ratings" => 1,
        "keywords" => ""
    );


    private function product_form() {

        $locale = $this->get_locale();
        $exit_link = clean_request("", array("aid", "section", "refs"), TRUE);
        $cid_hidden = array();

        if (isset($_POST['save_product'])) {

            $this->input_value = array(
                "id" => form_sanitizer($_POST['id'], 0, "id"),
                "cid" => isset($_POST['cid']) ? form_sanitizer($_POST['cid'], 0, "cid") : "", // this is a multiple
                "status" => isset($_POST['status']) ? 1 : 0,
                "title" => form_sanitizer($_POST['title'], "", "title"),
                "intro" => form_sanitizer($_POST['title'], "", "title"),
                "description" => form_sanitizer($_POST['title'], "", "title"),
                "user_id" => $this->get_userdata("user_id"),
                "user_ip" => USER_IP,
                "user_ip_type" => USER_IP_TYPE,
                "language" => form_sanitizer($_POST['language'], "", "language"),
                "price" => form_sanitizer($_POST['price'], "", "price"),
                "market_price" => form_sanitizer($_POST['market_price'], "", "market_price"),
                "quantity" => form_sanitizer($_POST['quantity'], "", "quantity"),
                "weight" => form_sanitizer($_POST['weight'], "", "weight"),
                "shippable" => form_sanitizer($_POST['shippable'], 0, "shippable"),
                "free_shipping" => form_sanitizer($_POST['free_shipping'], 0, "free_shipping"),
                "access" => form_sanitizer($_POST['access'], "", "access"),
                "datestamp" => time(),
                "allow_comments" => isset($_POST['allow_comments']) ? 1 : 0,
                "allow_ratings" => isset($_POST['allow_ratings']) ? 1 : 0,
                "keywords" => form_sanitizer($_POST['keywords'], "", "keywords"),
            );

            if (!empty($this->module)) {
                foreach($this->module as $module_name => $module_class) {
                    if (method_exists($this->module[$module_name], "get_form_input")) {
                        $this->input_value += $this->module[$module_name]->get_form_input($this->input_value);
                    }
                }
            }

            /*
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
            */

            if (\defender::safe()) {
                if (!empty($this->input_value['id']) && dbcount("(id)", DB_STORE_PRODUCTS, "id='".intval($this->input_value['id'])."'")
                ) {

                    dbquery_insert(DB_STORE_PRODUCTS, $this->input_value, "update");

                    addNotice("success", "Product successfully updated");

                } else {

                    dbquery_insert(DB_STORE_PRODUCTS, $this->input_value, "save");

                    addNotice("success", "Product successfully added");

                }

                redirect($exit_link);
            }
        }


        echo openform("product_form", "post", FUSION_REQUEST, array("class"=>"m-t-20", "enctype" => true));
        ?>

        <div class="row">
            <div class="col-xs-12 col-sm-7">

                <?php

                echo form_hidden("id", "", $this->input_value['id']);

                if (!empty($this->module)) {
                    foreach($this->module as $module_name => $module_class) {
                        if (method_exists($this->module[$module_name], "display_form_input")) {
                            echo $this->module[$module_name]->display_form_input($this->input_value, "top");
                        }
                    }
                }

                echo form_text("title", "Product Name", $this->input_value['title'], array("inline"=>TRUE, "required"=>TRUE));

                // run settings - multiple categories
                echo form_select_tree("cid[]", "Categories", $this->input_value['cid'], array(
                    "disable_opts" => $cid_hidden,
                    "hide_disabled" => TRUE,
                    "inline" => TRUE,
                    "no_root" => TRUE,
                    "multiple" => TRUE,
                    "required" => TRUE, // run settings
                    "width" => "100%",
                ), DB_STORE_CATS, "title", "cid", "parentid");

                // variants are set up in a separate matter
                /*
                echo form_select_tree("parentid", "Product Of", $this->input_value['cid'], array(
                    "disable_opts" => $id_hidden,
                    "hide_disabled" => TRUE,
                    "inline" => TRUE,
                    "width" => "300px",
                    "parent_value" => "New Product",
                ), DB_STORE_PRODUCTS, "title", "id", "parentid");
                */

                echo form_text("price", "Price", $this->input_value['price'],
                               array("inline"=>TRUE, "required"=>TRUE, "width"=>"180px", "prepend_value"=>"$",
                                     "regex"=>"\d{1,3}[,\\.]?(\\d{1,2})?",
                                     "error_text" => "Please fill in only numbers, decimal accepted"
                               )
                );

                echo form_text("market_price", "Market Price", $this->input_value['market_price'],
                               array("inline"=>TRUE, "required"=>TRUE, "width"=>"180px", "prepend_value"=>"$",
                                     "regex"=>"\d{1,3}[,\\.]?(\\d{1,2})?",
                                     "error_text" => "Please fill in only numbers, decimal accepted"
                               )
                );

                echo form_text("quantity", "Quantity in Stock", $this->input_value['quantity'],
                               array("inline"=>TRUE, "required"=>TRUE, "width"=>"200px", "type"=>"number"
                               )
                );

                echo form_text("weight", "Weight (kg)", $this->input_value['weight'],
                               array("inline"=>TRUE, "required"=>TRUE, "width"=>"200px",
                                     "regex"=>"\d{1,3}[,\\.]?(\\d{1,2})?",
                                     "error_text" => "Please fill in only numbers, decimal accepted"
                                     )
                );

                echo form_select("shippable", "Shippable", $this->input_value['shippable'],
                               array("options"=>array($locale['no'], $locale['yes']), "inline"=>TRUE, "width"=>"180px")
                );

                echo form_select("free_shipping", "Free Shipping", $this->input_value['free_shipping'],
                                 array("options"=>array($locale['no'], $locale['yes']), "inline"=>TRUE, "width"=>"180px")
                );

                echo form_select("access", "Visibility", $this->input_value['access'],
                                 array("options"=> fusion_get_groups(), "inline"=>TRUE,
                                 ));

                // run settings for the limited photo per product
                /*$this->store_settings['photo_per_product'] = 3;
                echo "<div class='row'>";
                echo "<label class='col-xs-12 col-sm-3'>Product Images</label>\n";
                echo "<div class='col-xs-12 col-sm-9 p-l-0 p-r-0'>\n";
                for ($i=1; $i<= $this->store_settings['photo_per_product']; $i++) {
                    echo "<div class='col-xs-12 col-sm-4'>\n";
                    echo form_fileinput("photo_".$i, "", "", array(
                        "upload_path" => STORE.'/product_img/',
                        "thumbnail" => TRUE,
                        "template" => "thumbnail",
                    ));
                    echo "</div>\n";
                }
                echo "</div>\n";
                echo "</div>\n";
                */

                echo form_textarea("intro", "Brief Description", $this->input_value['intro'], array(
                    "type" => "tinymce",
                    "tinymce" => "simple",
                    "inline" => TRUE,
                ));

                echo form_textarea("description", "Full Description", $this->input_value['description'], array(
                    "type" => "tinymce",
                    "tinymce" => "simple",
                    "inline" => TRUE,
                ));



                if (multilang_table("CMS")) {

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
                            "disabled" => $isDisabled ? TRUE : FALSE,
                        ));

                        if ($isDisabled) {
                            echo form_hidden("language[]", "", $language);
                        }
                    }

                    echo "</div>\n";
                    echo "</div>\n";

                } else {
                    echo form_hidden('language', '', $this->input_value['language']);
                }

                if (!empty($this->module)) {
                    foreach($this->module as $module_name => $module_class) {
                        if (method_exists($this->module[$module_name], "display_form_input")) {
                            echo $this->module[$module_name]->display_form_input($this->input_value, "bottom");
                        }
                    }
                }

                echo form_select("keywords", "Meta Keywords", $this->input_value['keywords'], array(
                    "options" => array(),
                    "width" => "100%",
                    "inline" => true,
                    "placeholder" => "Separate each keywords with a return key",
                    "tags" => true,
                    "multiple" => true
                ));

                // This one needs to load dynamically - set up in taxations, payments, and shipping to show
                // Load via ajax using standard fields to value with checkboxes to value.
                // use this [] -- label to value.
                ?>
            </div>
            <div class="col-xs-12 col-sm-4">
                <?php


                echo form_checkbox("status", "Available for Sale", $this->input_value['status'],
                                   array(
                                       "reverse_label" => TRUE,
                                   )
                );

                echo form_checkbox("allow_comments", "Allow Product Comments", $this->input_value['allow_comments'],
                                   array(
                                       "reverse_label"=>TRUE,
                                   )
                );
                echo form_checkbox("allow_ratings", "Allow Product Ratings", $this->input_value['allow_comments'],
                                   array(
                                       "reverse_label"=>TRUE,
                                   )
                );


                ?>


            </div>
        </div>
        <?php
        echo form_button("save_product", $locale['save_changes'], $locale['save'], array("class"=>"btn-primary"));
        echo closeform();
    }

    public function product_submissions() {

    }

    public function list_product() {
        global $userdata;
        ?>

        <div class="well">
            <table class="no-border">
                <td class="col-xs-6">
                    <?php echo form_text("product", "", "", array("placeholder"=>"Search for product")); ?>
                </td>
                <td class="p-l-10 p-r-10">
                    <?php echo form_select("product_cat", "", "", array("placeholder"=>"Category")); ?>
                </td>
                <td class="p-l-10 p-r-10">
                    <?php
                    $status = array(
                        "0" => "In Stock",
                        "1" => "Low Stock",
                        "2" => "Out of Stock"
                    );
                    echo form_select("product_status", "", "", array("options"=>$status, "placeholder"=>"Category")); ?>
                </td>
                <td class="p-l-10 p-r-10">
                    <?php echo form_button("search_product", "Search", "seach_product"); ?>
                </td>
            </table>
        </div>


        <table class="table m-t-20 table-striped table-hover">
            <thead>
            <tr>
                <th><?php echo form_checkbox("check_all", "", "", array("class"=>"m-0")) ?></th>
                <th></th>
                <th>Addon SKU</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>License</th>
                <th>Author</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?php echo form_checkbox("addon_id", "") ?></td>
                <td><?php echo form_button("set_active", "", "disabled", array("icon"=>"fa fa-checked-o fa-fw")) ?></td>
                <td>13580</td>
                <td>An addon title</td>
                <td>Category</td>
                <td>10.00</td>
                <td>AGPL3</td>
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
                    <a href=""><i class="fa fa-trash fa-fw"></i></a>
                </td>
            </tr>
            </tbody>
        </table>
    <?php
    }



}