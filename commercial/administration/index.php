<?php

require_once "../../../maincore.php";
require_once THEMES."templates/admin_header.php";

pageAccess("cms");

require_once COMMERCE."/autoloader.php";

new commerce\controller\admin_controller();

/** Code ends */



if (!isset($_GET['a_page'])) { $_GET['a_page'] = "Main"; }

if ($_GET['a_page'] == "Main") {
$tbl0 = "tbl1";
} else {
$tbl0 = "tbl2";
}

if ($_GET['a_page'] == "Categories") {
$tbl1 = "tbl1";
} else {
$tbl1 = "tbl2";
}

if ($_GET['a_page'] == "settings") {
$tbl2 = "tbl1";
} else {
$tbl2 = "tbl2";
}

if ($_GET['a_page'] == "photos") {
$tbl3 = "tbl1";
} else {
$tbl3 = "tbl2";
}

if ($_GET['a_page'] == "pfiles") {
$tbl4 = "tbl1";
} else {
$tbl4 = "tbl2";
}

if ($_GET['a_page'] == "versions") {
$tbl5 = "tbl1";
} else {
$tbl5 = "tbl2";
}

if ($_GET['a_page'] == "downloads") {
$tbl6 = "tbl1";
} else {
$tbl6 = "tbl2";
}

if ($_GET['a_page'] == "translations") {
$tbl7 = "tbl1";
} else {
$tbl7 = "tbl2";
}

if ($_GET['a_page'] == "submissions") {
$tbl8 = "tbl1";
} else {
$tbl8 = "tbl2";
}

if ($_GET['a_page'] == "Featured") {
$tbl9 = "tbl1";
} else {
$tbl9 = "tbl2";
}

//$countsubmissions = "".dbcount("(addon_id)", "".DB_ADDONS."", "addon_status = '1' OR addon_status = '2'")."";

//$counttranslations = "".dbcount("(trans_id)", "".DB_ADDONS_TRANS."", "trans_active = '1'")."";

echo "<table cellspacing='1' cellpadding='1' width='100%' ><tr>
<td align='center' class='".$tbl0."' width='1%'><a href='".FUSION_SELF.$aidlink."&amp;a_page=Main'>Main</a></td>
<td align='center' class='".$tbl3."' width='1%'><a href='".FUSION_SELF.$aidlink."&amp;a_page=photos'>Photo</a></td>
<td align='center' class='".$tbl1."' width='1%'><a href='".FUSION_SELF.$aidlink."&amp;a_page=Categories'>Category</a></td>
<td align='center' class='".$tbl2."' width='1%'><a href='".FUSION_SELF.$aidlink."&amp;a_page=settings'>Settings</a></td>
<td align='center' class='".$tbl8."' width='1%'><a href='".FUSION_SELF.$aidlink."&amp;a_page=submissions'>New Addons</a> <div class='countbox_bubble'></div></td></tr><tr>
<td align='center' class='".$tbl9."' width='1%'><a href='".FUSION_SELF.$aidlink."&amp;a_page=Featured'>Featured</a></td>
<td align='center' class='".$tbl4."' width='1%'><a href='".FUSION_SELF.$aidlink."&amp;a_page=pfiles'>Personal files</a></td>
<td align='center' class='".$tbl5."' width='1%'><a href='".FUSION_SELF.$aidlink."&amp;a_page=versions'>Versions</a></td>
<td align='center' class='".$tbl6."' width='1%'><a href='".FUSION_SELF.$aidlink."&amp;a_page=downloads'>Downloads</a></td>
<td align='center' class='".$tbl7."' width='1%'><a href='".FUSION_SELF.$aidlink."&amp;a_page=translations'>New Translations</a> <div class='countbox_bubble'></div></td>
</tr></table><div class='spacer'></div><div style='clear:both;'></div>";

if ($_GET['a_page'] == "Main") {
//include "addons.php";
}
elseif ($_GET['a_page'] == "Categories") {
//include "categories.php";
}
elseif ($_GET['a_page'] == "photos") {
include "photosadmin.php";
}
elseif ($_GET['a_page'] == "pfiles") {
include "pfiles.php";
}
elseif ($_GET['a_page'] == "versions") {
include "versions.php";
}
elseif ($_GET['a_page'] == "settings") {
include "settings.php";
}
elseif ($_GET['a_page'] == "downloads") {
include "downloads.php";
}
elseif ($_GET['a_page'] == "translations") {
include "translations.php";
}
elseif ($_GET['a_page'] == "submissions") {
include "submissions.php";
}
elseif ($_GET['a_page'] == "Featured") {
include "featured.php";
}

require_once THEMES."templates/footer.php";