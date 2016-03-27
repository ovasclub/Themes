<?php

namespace module\attributes;

use modules\attributes\model\attributes_resource;

class field_attributes extends attributes_resource {

    /**
     * Administration is for self administration
     */
    private $class_data = array(
        "field_class_id" => 0,
        "field_class_title" => "",
    );

    /**
     * Fixed name - administration method
     * Interface
     */
    function administration() {

        if (isset($_GET['class_id'])) {

            $tab_title['title'][] = "Attributes Category Listing";
            $tab_title['id'][] = "attributes_list";

            $tab_title['title'][] = isset($_GET['action']) ? "Edit Attributes" : "Add New Attributes";
            $tab_title['id'][] = "attributes_id";

            $active = 0;
            if (isset($_GET['action']) && $_GET['action'] == "edit" && isset($_GET['field_id'])) {
                $active = 1;
            }

            $tab_active = tab_active($tab_title, $active);

        } else {

            $tab_title['title'][] = "Class Listing";
            $tab_title['id'][] = "attributes_list";

            $tab_title['title'][] = isset($_GET['action']) ? "Edit Class" : "Add New Class";
            $tab_title['id'][] = "attributes_id";

            $tab_active = tab_active($tab_title, isset($_GET['action']) ? 1 : 0);

        }

        echo opentab($tab_title, $tab_active, "attr_class_tab", FALSE, "m-t-20");

        echo opentabbody($tab_title['title'][0], $tab_title['id'][0], $tab_active);

        if (isset($_GET['class_id'])) {

            $this->category_table();

        } else {

            $this->class_table();

        }

        echo closetabbody();

        echo opentabbody($tab_title['title'][1], $tab_title['id'][1], $tab_active);

        if (isset($_GET['class_id'])) {

            $this->attribute_form();

        } else {

            $this->class_form();

        }

        echo closetabbody();

        echo closetab();

    }

    /**
     * Class table
     */
    private function class_table() {

        $classes = $this->get_attributes_class();

        if (!empty($classes)) {
            ?>
            <table class="table table-responsive table-striped table-hover">
                <thead>
                <tr>
                    <th class="col-xs-1"></th>
                    <th>Class Name</th>
                    <th class="col-xs-2">Total Attributes <span class="badge m-l-10"><?php echo $this->get_class_attributes_count(NULL) ?></span></th>
                    <th class="col-xs-1">Class Id</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($classes as $class_id => $class_data) :
                    $edit_link = clean_request("action=edit&class_id=".$class_id, array("aid", "section", "refs"), TRUE);
                    $del_link = clean_request("action=del&class_id=".$class_id, array("aid", "section", "refs"), TRUE);
                    $access_link = clean_request("class_id=".$class_id, array("aid", "section", "refs"), TRUE);
                    $attribute_count = $this->get_class_attributes_count($class_id);
                    ?>
                    <tr>
                        <td>
                            <a href="<?php echo $edit_link ?>">Edit</a> -
                            <a href="<?php echo $del_link ?>">Delete</a>
                        </td>
                        <td>
                            <?php echo $class_data['field_class_title'] ?>
                        </td>
                        <td>
                            <a href="<?php echo $access_link ?>">Edit Attributes</a>
                            <span class="badge m-l-10"><?php echo $attribute_count ?></span>
                        </td>
                        <td>
                            <?php echo $class_data['field_class_id'] ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php
        } else {
            ?>
            <div class="well text-center m-t-20">There are no class defined.</div>
            <?php
        }

    }

    /**
     * Class Editor
     */
    private function class_form() {

        $locale = $this->get_locale();

        $exit_link = clean_request("", array("action", "class_id"), FALSE);

        if (isset($_GET['action']) && isset($_GET['class_id'])) {
            if (isnum($_GET['class_id'])) {
                $data = $this->get_attributes_class($_GET['class_id']);
                if (!empty($data)) {
                    switch($_GET['action']) {
                        case "edit":
                            $this->class_data = $data;
                            break;
                        case "delete":
                            if (!$this->get_attributes_count($_GET['class_id'], "class")) {
                                dbquery_insert(DB_COM_FIELD_CLASSES, $data, "delete");
                                addNotice("success", "Field Class successfully deleted");
                            }
                            break;
                    }
                }
            }
        }

        if (isset($_POST['save_class'])) {

            $this->class_data = array(
                "field_class_id" => form_sanitizer($_POST['field_class_id'], 0, "field_class_id"),
                "field_class_title" => form_sanitizer($_POST['field_class_title'], "", "field_class_title")
            );

            if (\defender::safe()) {
                if (dbcount("(field_class_id)", DB_COM_FIELD_CLASSES, "field_class_id = '".intval($this->class_data['field_class_id'])."'")) {
                    dbquery_insert(DB_COM_FIELD_CLASSES, $this->class_data, "update");
                } else {
                    dbquery_insert(DB_COM_FIELD_CLASSES, $this->class_data, "save");
                }

            }
            redirect($exit_link);
        }

        echo openform("attr_class_frm", "post", FUSION_REQUEST).

        form_hidden("field_class_id", "", $this->class_data['field_class_id']).

        form_text("field_class_title", "Class Name", $this->class_data['field_class_title'], array("required"=>TRUE, "placeholder"=>"Grouping Class (i.e. Computers, T-Shirts)")).

        form_button("save_class", $locale['save_changes'], $locale['save_changes'], array("class"=>"btn-primary")).

        closeform();
    }

    /**
     * Class table
     */
    private $field_cat_data = array(
        "field_cat_id" => 0,
        "field_cat_title" => ""
    );

    private function category_table() {

        $locale = $this->get_locale();

        $field_category_exit = clean_request("", array("action", "field_cat_id"), FALSE);

        $field_cat_edit = FALSE;

        if (isset($_GET['action']) && isset($_GET['field_cat_id']) && isnum($_GET['field_cat_id'])) {

            $data = $this->get_field_cats(intval($_GET['field_cat_id']));

            if (!empty($data)) {

                switch($_GET['action']) {

                    case "edit":
                        $field_cat_edit = TRUE;
                        $this->field_cat_data = $data;
                        break;

                    case "del":
                        if (!dbcount("(field_id)", DB_COM_FIELDS, "field_cat='".intval($_GET['field_cat_id'])."'")) {
                            dbquery_insert(DB_COM_FIELD_CATS, $data, "delete");
                            addNotice("success", "Field group has been deleted");
                        } else {
                            addNotice("danger", "Cannot delete this group as it still contains attributes");
                            redirect($field_category_exit);
                        }
                }
            } else {
                redirect($field_category_exit);
            }

        } else {

            add_to_jquery("
            $('#add_group').bind('click', function(e) {
                $('#group_div').show();
            });
            $('#cancel').bind('click', function(e) {
                $('#group_div').hide();
            });
            ");

        }

       if (isset($_POST['save_category'])) {

            $this->field_cat_data = array(
                "field_cat_id" => form_sanitizer($_POST['field_cat_id'], 0, "field_cat_id"),
                "field_cat_class" => isset($_GET['class_id']) && isnum($_GET['class_id']) ? intval($_GET['class_id']) : 0,
                "field_cat_title" => form_sanitizer($_POST['field_cat_title'], "", "field_cat_title"),
                "field_cat_order" => dbresult(dbquery("SELECT MAX(field_cat_order) FROM ".DB_COM_FIELD_CATS), 0)+1,
            );

            if (\defender::safe()) {
                if (dbcount("(field_cat_id)", DB_COM_FIELD_CATS, "field_cat_id='".$this->field_cat_data['field_cat_id']."'")) {
                    dbquery_insert(DB_COM_FIELD_CATS, $this->field_cat_data, "update");
                    addNotice("success", "Field group successfully updated");
                } else {
                    dbquery_insert(DB_COM_FIELD_CATS, $this->field_cat_data, "save");
                    addNotice("success", "Field group successfully created");
                }

                redirect($field_category_exit);
            }
        }

        $filter = isset($_GET['class_id']) && isnum($_GET['class_id']) ? "WHERE field_cat_class=".intval($_GET['class_id']) : NULL;

        $filter2 = isset($_GET['class_id']) && isnum($_GET['class_id']) ? "WHERE field_class=".intval($_GET['class_id']) : NULL;

        $field_cats = $this->get_field_cats(NULL, $filter);

        $fields = $this->get_fields(NULL, $filter2);

        // global list
        $no_group = array();
        $group = array();
        if (!empty($fields)) {
            foreach($fields as $field_id => $field_data) {
                if (!$field_data['field_cat']) {
                    $no_group[] = $field_data;
                } else {
                    $group[$field_data['field_cat']][$field_id] = $field_data;
                }
            }
        }
        echo form_button("add_group", "Add New Group", "add_group", array("class"=>"btn-default", "type"=>"button")); ?>

        <!---form --->
        <div id="group_div" class="list-group-item" <?php echo ($field_cat_edit == FALSE ? "style=\"display:none\"" : "") ?>>
            <?php
            echo openform("field_cat_form", "post", FUSION_REQUEST).
                form_hidden("field_cat_id", "", $this->field_cat_data['field_cat_id']).
                form_text("field_cat_title", "New Group Title", $this->field_cat_data['field_cat_title'], array("required"=>TRUE, "placeholder"=>"i.e Display, Battery, Size & Weight")).
                form_button("save_category", $locale['save_changes'], $locale['save_changes'], array("class"=>"btn-primary")).
                form_button("cancel", "Cancel", "cancel", array("class"=>"btn-default m-l-10", "type"=>"button"));
                closeform();
            ?>
        </div>
        <!---endform-->

        <div class="list-group">
            <div class="list-group-item">
                <span class="list-group-item-heading">
                    <strong>Ungrouped</strong>
                </span>
            </div>

        <?php
        if (!empty($no_group)) : ?>

            <div class="list-group-item">
            <table class="table table-responsive table-striped table-hover">
            <thead>
            <tr>
                <th class="col-xs-1"></th>
                <th>Attributes</th>
                <th class="col-xs-2">Type</th>
                <th class="col-xs-1">Attributes Id</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($no_group as $field_data) :

                $edit_link = clean_request("action=edit&field_id=".$field_data['field_id'],
                                           array("aid", "section", "refs", "class_id",), TRUE);
                $del_link = clean_request("action=del&field_id=".$field_data['field_id'],
                                          array("aid", "section", "refs", "class_id",), TRUE);
                ?>
                <tr>
                    <td>
                        <a href="<?php echo $edit_link ?>"><?php echo $locale['edit'] ?></a> -
                        <a href="<?php echo $del_link ?>"><?php echo $locale['delete'] ?></a>
                    </td>
                    <td>
                        <?php echo $field_data['field_title'] ?>
                    </td>
                    <td>
                        <?php echo $this->get_field_types($field_data['field_type']) ?>
                    </td>
                    <td>
                        <?php echo $field_data['field_id'] ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php else: ?>
            <div class="list-group-item text-center">
                There are no attributes in this group
            </div>
        <?php endif; ?>
        </div>

        <?php
        if (!empty($field_cats)) {

            foreach($field_cats as $field_cat_id => $field_cat_data) :
                $cat_edit_link = clean_request("action=edit&field_cat_id=".$field_cat_id, array("aid", "section", "refs","class_id", ), TRUE);
                $cat_delete_link = clean_request("action=del&field_cat_id=".$field_cat_id, array("aid", "section", "refs","class_id", ), TRUE);
                ?>

                <div class="list-group">

                    <div class="list-group-item">
                        <span class="list-group-item-heading">
                            <strong>
                            <?php echo $field_cat_data['field_cat_title'] ?>
                            </strong>
                            <span class="category_action m-l-20">
                                <a href="<?php echo $cat_edit_link ?>"><?php echo $locale['edit'] ?></a> -
                                <a href="<?php echo $cat_delete_link ?>"><?php echo $locale['delete'] ?></a>
                            </span>
                        </span>
                    </div>

                    <?php
                    if (!empty($group) && isset($group[$field_cat_id])) : ?>
                        <div class="list-group-item">
                            <table class="table table-responsive table-striped table-hover">
                            <thead>
                            <tr>
                                <th class="col-xs-1"></th>
                                <th>Attributes</th>
                                <th class="col-xs-2">Type</th>
                                <th class="col-xs-1">Attributes Id</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($group[$field_cat_id] as $field_data) :

                                $edit_link = clean_request("action=edit&field_id=".$field_data['field_id'], array("aid", "section", "refs","class_id", ), TRUE);
                                $del_link = clean_request("action=del&field_id=".$field_data['field_id'], array("aid", "section", "refs","class_id", ), TRUE);
                                ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo $edit_link ?>"><?php echo $locale['edit'] ?></a> -
                                        <a href="<?php echo $del_link ?>"><?php echo $locale['delete'] ?></a>
                                    </td>
                                    <td>
                                        <?php echo $field_data['field_title'] ?>
                                    </td>
                                    <td>
                                        <?php echo $this->get_field_types($field_data['field_type']) ?>
                                    </td>
                                    <td>
                                        <?php echo $field_data['field_id'] ?>
                                    </td>
                                </tr>

                        <?php endforeach; ?>
                            </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <div class="list-group-item text-center">
                            There are no attributes in this group
                        </div>
                    <?php endif; ?>
                    </div>
            <?php endforeach;
        }

    }

    /**
     * Attribute Editor
     */

    private $attr_data = array(
        "field_id" => 0,
        "field_cat" => 0,
        "field_title" => "",
        "field_description" => "",
        "field_type" => "",
        "field_language" => LANGUAGE,
        "field_order" => "",
    );

    private function attribute_form() {

        $locale = $this->get_locale();

        $exit_link = clean_request("", array("action"), FALSE);

        if (isset($_GET['action']) && isset($_GET['field_id'])) {
            if (isnum($_GET['field_id'])) {

                $data = $this->get_fields($_GET['field_id']);

                if (!empty($data)) {
                    switch($_GET['action']) {
                        case "edit":
                            $this->attr_data = $data;
                            break;
                        case "del":

                            dbquery("DELETE FROM ".DB_COM_FIELD_OPTIONS." WHERE field_option_parent='".intval($data['field_id'])."'");

                            dbquery("DELETE FROM ".DB_COM_FIELDS." WHERE field_id='".intval($data['field_id'])."'");

                            addNotice("success", "Attributes successfully deleted");

                            redirect($exit_link);

                            break;
                    }
                } else {

                    redirect($exit_link);

                }
            }
        }

        if (isset($_POST['save_option']) && $this->attr_data['field_id']) {
            $option_data = array(
                "field_option_id" => 0,
                "field_option_value" => form_sanitizer($_POST['new_option_value'], "", "new_option_value"),
                "field_option_parent" => $this->attr_data['field_id'],
                "field_option_order" => dbresult(dbquery("SELECT MAX(field_option_order) FROM ".DB_COM_FIELD_OPTIONS." WHERE field_option_parent='".$this->attr_data['field_id']."'"),
                                                 0) + 1,
            );
            dbquery_insert(DB_COM_FIELD_OPTIONS, $option_data, "save");
            addNotice("success", "Attributes option successfully added");
            redirect(FUSION_REQUEST);
        }
        elseif (isset($_POST['option_delete']) && isnum($_POST['option_delete']) && $this->attr_data['field_id']) {

            $value = $_POST['option_delete'];

            if (\defender::safe()) {
                // reorder all options
                dbquery_order(DB_COM_FIELD_OPTIONS,
                              dbresult(dbquery("SELECT field_option_order FROM ".DB_COM_FIELD_OPTIONS." WHERE field_option_id='".$this->attr_data['field_id']."'"), 0),
                              "field_option_order",
                              $this->attr_data['field_id'],
                              "field_option_id",
                              $this->attr_data['field_id'],
                              "field_option_parent",
                              FALSE,
                              NULL,
                              "delete"
                              );

                dbquery("DELETE FROM ".DB_COM_FIELD_OPTIONS." WHERE field_option_id='".intval($value)."'");

                addNotice("success", "Attributes option successfully deleted");

                redirect(FUSION_REQUEST);
            }

        }

        else if (isset($_POST['option_update']) && isnum($_POST['option_update']) && $this->attr_data['field_id']) {

            // Update the option

            $value = $_POST['option_update'];

            if (isset($_POST['option_value'][$value])) {

                $option_data = array(
                    "field_option_id" => intval($value),
                    "field_option_value" => form_sanitizer($_POST['option_value'][$value], "", "option_value[$value]"),
                    "field_option_parent" => $this->attr_data['field_id'],
                );

                if (\defender::safe()) {

                    dbquery_insert(DB_COM_FIELD_OPTIONS, $option_data, "update");
                    addNotice("success", "Attributes option successfully updated");

                    redirect(FUSION_REQUEST);
                }
            }
        }

        elseif (isset($_POST['apply_modifier']) && isnum($_POST['apply_modifier']) &&
            dbcount("(field_option_id)", DB_COM_FIELD_OPTIONS, "field_option_id='".intval($_POST['apply_modifier']."'")."'")
        ) {

            $value = $_POST['apply_modifier'];

            if (isset($_POST['option_price_modifier'][$value]) or isset($_POST['option_weight_modifier'][$value])) {

                $option_data = array(
                    "field_option_id" => intval($value),

                    "field_option_price_modifier" => form_sanitizer($_POST['option_price_modifier'][$value], "", "option_price_modifier[$value]"),

                    "field_option_weight_modifier" => form_sanitizer($_POST['option_weight_modifier'][$value], "", "option_weight_modifier[$value]"),

                );

                if (\defender::safe()) {

                    dbquery_insert(DB_COM_FIELD_OPTIONS, $option_data, "update");

                    addNotice("success", "Attributes option modifiers successfully updated");

                    redirect(FUSION_REQUEST);
                }
            }
        }

        else if (isset($_POST['save_attr'])) {

            $this->attr_data = array(
                "field_id" => form_sanitizer($_POST['field_id'], 0, "field_id"),
                "field_cat" => isset($_POST['field_cat']) ? form_sanitizer($_POST['field_cat'], "", "field_cat") : 0,
                "field_class" => isset($_GET['class_id']) && isnum($_GET['class_id']) ? $_GET['class_id'] : 0,
                "field_description" => form_sanitizer($_POST['field_description'], "", "field_description"),
                "field_title" => form_sanitizer($_POST['field_title'], "", "field_title"),
                "field_type" => form_sanitizer($_POST['field_type'], "", "field_type"),
                "field_language" => form_sanitizer($_POST['field_language'], LANGUAGE, "field_language"),
                "field_order" => dbresult(dbquery("SELECT MAX(field_order) FROM ".DB_COM_FIELDS), 0)+1,
            );

            if (\defender::safe()) {

                if (dbcount("(field_id)", DB_COM_FIELDS, "field_id=".$this->attr_data['field_id'])) {

                    dbquery_insert(DB_COM_FIELDS, $this->attr_data, "update");

                    addNotice("success", "Attributes successfully updated");

                } else {

                    dbquery_insert(DB_COM_FIELDS, $this->attr_data, "save");

                    $this->attr_data['field_id'] = dblastid();

                    addNotice("success", "Attributes successfully created");

                }

                if (!empty($this->get_field_options($this->attr_data['field_id'])) || ($this->attr_data['field_type'] == 3 || $this->attr_data['field_type'] == 4)) {

                    redirect($exit_link);

                } else {

                    addNotice("success", "Please define your attribute options");

                    redirect(clean_request("action=edit&field_id=".$this->attr_data['field_id'], array("aid", "section", "refs", "class_id"), TRUE));

                }

            }
        }

        echo openform("attr_frm", "post", FUSION_REQUEST);
        ?>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <?php

                $group_opts[0] = "No Group";

                $group_opts += $this->get_field_cat_opts();

                echo form_hidden("field_id", "", $this->attr_data['field_id']).

                    form_text("field_title", "Attribute Name", $this->attr_data['field_title'], array("required"=>TRUE, "placeholder"=>"i.e. Weight, Speed, Color, Capacity (MB)")).

                    form_textarea("field_description", "Attribute Description", $this->attr_data['field_description'], array("type"=>"tinymce", "tinymce"=>"simple")).

                    form_select("field_cat", "Attribute Group", $this->attr_data['field_cat'], array("options"=>$group_opts)).

                    form_select("field_language[]", "Attribute Language", $this->attr_data['field_language'], array(
                        "options"=>fusion_get_enabled_languages(), "multiple"=>TRUE, "required"=>TRUE, "width"=>"100%")
                    );
                ?>
            </div>
            <div class="col-xs-12 col-sm-6">

                <?php echo form_select("field_type", "Attribute Type", $this->attr_data['field_type'], array("options"=>$this->get_field_types())); ?>

                <div id="option_table">

                    <?php
                    // Do a standalone option form -- must click create option.
                    if (isset($_GET['action']) && isset($_GET['field_id']) && isnum($_GET['field_id'])) :

                        $opts = $this->get_field_options($_GET['field_id']);

                        if (!empty($this->get_field_options($_GET['field_id']))) : ?>

                            <div class="list-group-item">
                                <table class="table">

                            <?php foreach($opts as $option_id => $options) : ?>

                                <tr>
                                <td>
                                <?php echo
                                    form_text("option_value[$option_id]", "Attribute", $options['field_option_value'],
                                              array("input_id"=>"options_value_".$option_id,
                                                    "required"=>TRUE,
                                                    "class"=>"option_values m-b-0",
                                                    "append_button" => TRUE,
                                                    "append_type" => "button",
                                                    "append_form_value" => $option_id,
                                                    "append_class" => "btn-default open_modifier",
                                                    "append_value" => "Add Modifiers",
                                                    "append_button_name" => "modifier_btn",
                                              )
                                    );
                                    ?>

                                    <div class="modifier_table well" id="modifier_<?php echo $option_id ?>" style="display:none;">
                                        <?php
                                        echo form_text("option_price_modifier[$option_id]", "Price Modifier", $options['field_option_price_modifier'],
                                        array('input_id' => 'price_modifier_'.$option_id, 'placeholder'=>'values: 20%, +20%, -20%, -10%, 15.50, -15.50',
                                              "regex" => "([+-]|)([0-9.]+)([%]|)",
                                              "error_text" => "Input must contain quantifier +digit% in % or numbers only. See example in placeholder",
                                        )
                                        ) .
                                        form_text("option_weight_modifier[$option_id]", "Weight Modifier", $options['field_option_weight_modifier'],
                                        array('input_id' => 'weight_modifier_'.$option_id, 'placeholder'=>'values: 20%, +20%, -20%, 15.50, -15.50',
                                              "regex" => "([+-]|)([0-9.]+)([%]|)",
                                              "error_text" => "Input must contain quantifier +digit% in % or numbers only. See example in placeholder",
                                              )
                                        ).
                                        form_button("apply_modifier", "Apply Modifier", $option_id,
                                                    array(
                                                        'input_id' => 'apply_modifier_'.$option_id,
                                                        "class"=>"btn-primary m-r-10"
                                                    )
                                        ).
                                        form_button("cancel_modifier[$option_id]", "Cancel", "cancel",
                                                    array(
                                                        'input_id' => 'cancel_modifier_'.$option_id,
                                                        "class"=>"btn-default cancel_modifier",
                                                        "type" => "button"
                                                    )
                                        );
                                        ?>
                                    </div>

                                    <div class="option_buttons" style="display:none;">
                                        <?php
                                        echo
                                            form_button("option_update", "Update", $option_id, array("input_id"=>"option_update_".$option_id, "class"=>"btn-primary m-r-10")) .
                                            form_button("option_delete", "Delete", $option_id, array("input_id"=>"option_delete_".$option_id, "class"=>"btn-default m-r-10"));
                                        ?>
                                    </div>

                                </td>
                                </tr>

                            <?php endforeach; ?>
                                </table>
                            </div>

                            <?php
                            add_to_jquery("
                            $('.option_values input').focusin(
                            function(e) {
                                $('.option_buttons').hide();
                                $(this).closest('tr').find('.option_buttons').show();
                            }
                            );
                            $('.open_modifier').bind('click', function(e) {
                                var dataPoint = $(this).val();
                                $('.modifier_table').hide();
                                $('#modifier_'+dataPoint).show();
                            });
                            $('.cancel_modifier').bind('click', function(e) {
                                $('.modifier_table').hide();
                            });

                            var init_val = $('#field_type').val();
                            if ((init_val == '1') || (init_val == '2')) {
                                $('#option_table').show();
                            } else {
                                $('#option_table').hide();
                            }
                            $('#field_type').bind('change', function(e) {
                                var val = $(this).val();
                                if ((val == 1) || (val == 2)) {
                                    $('#option_table').show();
                                } else {
                                    $('#option_table').hide();
                                }
                            });
                            ");
                            ?>


                        <?php endif; ?>

                        <div class="list-group-item">
                            <div class="row">
                                <div class="col-xs-8">
                                    <?php echo form_text("new_option_value", "", "", array("required"=>TRUE))?>
                                </div>
                                <div class="col-xs-4">
                                    <?php echo form_button("save_option", "Add New Option", "", array("class"=>"btn-success")) ?>
                                </div>
                            </div>
                        </div>

                    <?php endif; ?>

                </div>

            </div>
        </div>


            <?php

            echo form_button("save_attr", $this->attr_data['field_id'] ? $locale['save_changes'] : "Save and Create Options", $locale['save_changes'],
                             array("class"=> $this->attr_data['field_id'] ? "btn-primary" : "btn-success")
                ).

            closeform();
    }

    // This is for product administration that creates a special tab - todo later in another file
    function product_administration() {

    }


}