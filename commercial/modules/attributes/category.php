<?php

/**
 * Catalog/Category Page
 * Inserting a custom field into the category page
 * class name is module callback name in module db
 */

namespace module\attributes;

use modules\attributes\model\attributes_resource;

class field_attributes extends attributes_resource {

    private $data = array(
      "class" => 0
    );

    public function __construct() {
        parent::__construct();
    }

    // return the input post to category
    public function get_form_input() {
        if (isset($_POST['class'])) {
            return array("class" => form_sanitizer($_POST['class'], 0, "class"));
        }
        return array("class"=>0);
    }

    // Add input to category form
    public function display_form_input($data, $position) {

        if (isset($data['class'])) {
            $this->data['class'] = $data['class'];
        }

        $class = $this->get_attributes_class();

        $class_opts[0] = "Do not show filter";

        if (!empty($class)) {

            foreach($class as $class_data) {

                $class_opts[$class_data['field_class_id']] = $class_data['field_class_title'];

            }
        }

        switch ($position) {
            case "top":
                break;
            case "middle":
                break;
            case "bottom":
                return form_select("class", "Select Category Class", $this->data['class'], array(
                    "options" => $class_opts, "inline" => TRUE, "width" => "300px"
                ));
                break;
        }

        return NULL;
    }

}