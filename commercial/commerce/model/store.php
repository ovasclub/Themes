<?php

namespace commerce\model;

class store {

    private $username = 0;

    private $settings = array();

    private $store_settings = array();

    private $locale = array();

    private $addon_ratings = array();

    public function __construct() {

        global $locale, $userdata;

        $this->username = $_SERVER['REMOTE_ADDR'];

        if (iMEMBER) {
            $this->username = $userdata['user_id'];
        }


        $this->settings = fusion_get_settings();

        $this->store_settings = dbarray(dbquery("SELECT * FROM ".DB_ADDONS_SETTINGS.""));

        if (isset($_GET['category']) && !isnum($_GET['category'])) die("Denied");
        if (isset($_GET['addon_id']) && !isnum($_GET['addon_id'])) die("Denied");
        if (!isset($_GET['rowstart']) || !isnum($_GET['rowstart'])) { $_GET['rowstart'] = 0; }

        if (isset($_POST['FilterSelect']) && !isnum($_POST['FilterSelect'])) die("Denied");

        if (file_exists(INFUSIONS."addondb/locale/".$this->settings['locale'].".php")) {
            include INFUSIONS."addondb/locale/".$this->settings['locale'].".php";
        } else {
            include INFUSIONS."addondb/locale/English.php";
        }

        $this->locale = $locale;

        $this->addon_ratings = array(1 => $locale['func001'], $locale['func002'], $locale['func003'], $locale['func004'], $locale['func005']);

        $this->addon_types = array(1 => $locale['func006'], $locale['func007'], $locale['func008'], $locale['func010'],$locale['func025'],$locale['func026']);

        $this->addon_status = array($locale['func011'], $locale['func012'],$locale['func013'],$locale['func014'], $locale['func015']);

        $this->addon_orderby = array(
            "addon_name" => $locale['func016'],
            "addon_author_name" => $locale['func017'],
            "addon_date" => $locale['func018']
        );

        $this->addon_orderby_dir = array(
            "ASC" => $locale['func023'],
            "DESC" => $locale['func024']
        );

        $this->get_type = "";

        $this->addon_types = "";

        // Screen Shots
        $this->addon_upload_dir_img = ADDON_SCRN;
        $this->addon_upload_exts_img = array(
            "png" => "image/png",
            "PNG" => "image/png",
            "jpg" => "image/jpg",
            "JPG" => "image/jpg",
            "GIF" => "image/gif",
            "gif" => "image/gif"
        );

        $this->addon_upload_maxsize_img = 5000000;

        // Translations
        $this->trans_upload_dir = ADDON."files/trans/";

        $this->trans_upload_exts = array(
            "zip" => "application/zip",
            "rar" => "application/zip",
            "tar" => "application/x-tar",
            "tar.gz" => "application/x-gzip"
        );

        $this->trans_upload_maxsize = 5000000;

        $this->addon_upload_dir = ADDON."files/";

        $this->addon_upload_prefix = "submitted_addon_";

        $this->addon_list_dateformat = "%d/%m-%Y";

        $this->addon_upload_exts = array(
            "zip" => "application/zip",
            "rar" => "application/zip",
            "tar" => "application/x-tar",
            "tar.gz" => "application/x-gzip"
        );

        $this->addon_upload_maxsize = 5000000;

        $this->get_type = array(
            'Infusion' => 1,
            'Theme' => 2,
            'Panel' => 3,
            'Widget' => 4,
            'Plugin' => 5,
            'Other' => 6
        );

        $this->addon_types = array(1 => "Infusion","Theme","Panel","Widget","Plugin","Other",);
    }

}