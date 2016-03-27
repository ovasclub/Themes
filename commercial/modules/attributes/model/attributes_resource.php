<?php

namespace modules\attributes\model;

if (!defined("DB_COM_FIELD_CLASSES")) define("DB_COM_FIELD_CLASSES", DB_PREFIX."commerce_product_field_classes");

if (!defined("DB_COM_FIELD_CATS")) define("DB_COM_FIELD_CATS", DB_PREFIX."commerce_product_field_cats");

if (!defined("DB_COM_FIELDS")) define("DB_COM_FIELDS", DB_PREFIX."commerce_product_fields");

if (!defined("DB_COM_FIELD_OPTIONS")) define("DB_COM_FIELD_OPTIONS", DB_PREFIX."commerce_product_field_options");

if (!defined("DB_COM_FIELD_ORDERS")) define("DB_COM_FIELD_ORDERS", DB_PREFIX."commerce_product_fields_orders");


/**
 * Class attributes_resource
 * Compiler and resources such as query and information arrays
 */

abstract class attributes_resource extends \commerce\model\admin_resource {

    public function __construct() {
        parent::__construct();
    }

    protected static $classes;

    /**
     * Get all or a single field class
     * @param null $class_id
     * @return null
     */
    protected function get_attributes_class($class_id = NULL) {
        if (empty(self::$classes)) {
            $result = dbquery("SELECT * FROM ".DB_COM_FIELD_CLASSES);
            if (dbrows($result)>0) {
                while ($data = dbarray($result)) {
                    self::$classes[$data['field_class_id']] = $data;
                }
            }
        }
        return $class_id === NULL ? self::$classes : (isset(self::$classes[$class_id]) ? self::$classes[$class_id] : NULL);
    }

    /**
     * @param null   $id - the class_id or field_cat_id
     */
    protected function get_class_attributes_count($id = NULL) {
        if ($id === NULL) {
            $count = dbcount("(field_id)", DB_COM_FIELDS);
            return ($count) ? $count : 0;
        } else {
            $count = dbcount("(field_id)", DB_COM_FIELDS, "field_class='".intval($id)."'");
            return ($count) ? $count : 0;
        }
    }

    protected static $field_cats;

    protected function get_field_cat_opts() {
        $opts = array();
        $field_category = $this->get_field_cats();
        if (!empty($field_category)) {
            foreach($field_category as $cat_id => $cat_data) {
                $opts[$cat_id] = $cat_data['field_cat_title'];
            }
        }
        return $opts;
    }

    /**
     * Get field grouping category
     * @param null $cat_id
     * @return null
     */
    protected function get_field_cats($cat_id = NULL, $filter = NULL) {

        if (empty(self::$field_cats)) {
            $result = dbquery("SELECT * FROM ".DB_COM_FIELD_CATS." $filter ORDER BY field_cat_order ASC");
            if (dbrows($result)>0) {
                while ($data = dbarray($result)) {
                    self::$field_cats[$data['field_cat_id']] = $data;
                }
            }
        }

        return $cat_id === NULL ? self::$field_cats : (isset(self::$field_cats[$cat_id]) ? self::$field_cats[$cat_id] : NULL);
    }

    /**
     * Get field grouping category
     * @param null $cat_id
     * @return null
     */
    protected  static $fields;

    protected function get_fields($field_id = NULL, $filter = NULL) {

        if (empty(self::$fields)) {
            $result = dbquery("SELECT * FROM ".DB_COM_FIELDS." $filter ORDER BY field_order ASC");
            if (dbrows($result)>0) {
                while ($data = dbarray($result)) {
                    self::$fields[$data['field_id']] = $data;
                }
            }
        }

        return $field_id === NULL ? self::$fields : (isset(self::$fields[$field_id]) ? self::$fields[$field_id] : NULL);
    }

    protected static $field_options;

    protected function get_field_options($field_id = NULL, $filter = NULL) {

        if (empty(self::$field_options)) {
            $result = dbquery("SELECT * FROM ".DB_COM_FIELD_OPTIONS." $filter ORDER BY field_option_order ASC");
            if (dbrows($result)>0) {
                while ($data = dbarray($result)) {
                    self::$field_options[$data['field_option_parent']][$data['field_option_id']] = $data;
                }
            }
        }

        return $field_id === NULL ? self::$field_options : (isset(self::$field_options[$field_id]) ? self::$field_options[$field_id] : NULL);
    }


    /**
     * Type of attributes
     * @return array
     */
    protected function get_field_types($id = NULL) {
        $array = array(
            1 => "Plain Field",
            2 => "Multiple Selector",
            3 => "Text Area",
            4 => "Yes or No",
        );

        return $id === NULL ? $array : (isset($array[$id]) ? $array[$id] : NULL);
    }

}