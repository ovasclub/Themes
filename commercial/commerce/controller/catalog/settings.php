<?php
namespace commerce\controller\catalog;

use commerce\model\admin_resource;

class settings extends  admin_resource {

    public function __construct() {

        parent::__construct();

    }

    // Model for Admin
    public function administration() {

        // list and new together..
        $tab_title['title'][] = "Add New Category";
        $tab_title['id'][] = "add_cat";

        $tab_title['title'][] = "All Categories";
        $tab_title['id'][] = "cat_list";

        $tab_active = tab_active($tab_title, 0);

        echo opentab($tab_title, $tab_active, "app_cat", FALSE, "m-t-20");
        echo opentabbody($tab_title['title'][0], $tab_title['id'][0], $tab_active);
        $this->add_category();
        echo closetabbody();
        echo opentabbody($tab_title['title'][1], $tab_title['id'][1], $tab_active);
        $this->list_category();
        echo closetabbody();
        echo closetab();
    }


    public function add_category() {
        ?>

        <?php
    }

    public function category_submissions() {

    }

    public function list_category() {
        global $userdata;
        ?>
        <table class="table m-t-20 table-striped table-hover">
            <thead>
            <tr>
                <th></th>
                <th>Category</th>
                <th>Featured</th>
                <th>Subcategory</th>
                <th>Apps</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?php echo form_button("set_active", "", "disabled", array("icon"=>"fa fa-checked-o fa-fw")) ?></td>
                <td>Product Category</td>
                <td>0</td>
                <td>2</td>
                <td>6</td>
                <td>
                    <a href=""><i class="fa fa-trash fa-fw"></i></a>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }


}