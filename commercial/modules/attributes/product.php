<?php

/**
 * Catalog/Product Page
 * Inserting a custom field into the category page
 * class name is module callback name in module db
 */

namespace module\attributes;

use modules\attributes\model\attributes_resource;

class field_attributes extends attributes_resource {

    private $data = array(
        "sku" => "",
    );

    public function __construct() {
        parent::__construct();
    }

    // for a new tab, need another function





    // return the input post to category
    public function get_form_input() {
        if (isset($_POST['sku'])) {
            return array("sku" => form_sanitizer($_POST['sku'], 0, "sku"));
        }
        return array("sku"=>"");
    }

    // Add input to category form
    public function display_form_input($data, $position) {

        if (isset($data['class'])) {
            $this->data['sku'] = $data['sku'];
        }

        switch($position) {
            case "top":
                echo form_text("sku", "Product SKU", $this->data['sku'], array("inline" => TRUE, "width" => "300px"));
                break;
            case "middle":
                break;
            case "bottom":
                break;
        }

        return NULL;


    }

}