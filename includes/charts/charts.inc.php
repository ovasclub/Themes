<?php
## Charting IO Components chart.js
function chart_doughnut($id, $title = FALSE, $data, $array = FALSE)
{

    if (!defined("chart")) {
        add_to_head("<script src='" . INCLUDES . "charts/Chart.min.js'></script>\n");
        define("chart", TRUE);
    }

    // require - data['value'], ['color'], ['highlight'], ['label']
    $id = $id ? $id : '';

    if (!is_array($array)) {
        $title_class = '';
        $stats = '';
        $icon = '';
        $width = '80';
        $height = '80';
    } else {
        $stats = (array_key_exists('stats', $array)) && $array['stats'] ? $array['stats'] : '';
        $title_class = (array_key_exists('title_class', $array)) && $array['class'] ? $array['class'] : '';
        $icon = (array_key_exists('icon', $array)) && $array['icon'] ? $array['icon'] : '';
        $width = (array_key_exists('width', $array)) && isnum($array['width']) ? $array['width'] : '80';
        $height = (array_key_exists('height', $array)) && isnum($array['height']) ? $array['height'] : '80';
    }

    if (count($data) > 0) {
        $js = "[";
        foreach ($data as $key => $cdata) {
            $js .= "{";
            foreach ($cdata as $arr => $value) {
                $js .= (isnum($value)) ? "$arr:$value," : "$arr:'$value',";
            }
            $js .= "},";
        }
        $js .= "];";
    }

    $html = "<div class='chart-container text-center' style='width:" . $width . "px; margin: 0 auto;'>\n";
    $html .= ($icon) ? "<div class='chart-icon' style='display:block; position: relative; width:100%; margin: 40% auto -60%;'>\n<i class='" . $icon . "'></i></div>\n" : '';
    $html .= "<div id='canvas-holder'>\n";
    $html .= "<canvas id='" . $id . "' width='" . $width . "' height='" . $height . "'/>\n";
    $html .= "</div>\n";
    $html .= "</div>\n";
    if ($stats || $title) {
        $html .= "<div class='text-center'>\n";
        $html .= ($stats) ? "<span class='strong text-dark'>" . $stats . "</span>" : '';
        $html .= ($title) ? "<div class='text-darker " . $title_class . "' >$title</div>\n" : '';
        $html .= "</div>\n";
    }

    /*
     * var pieData = [
            {
                value: 30,
                'color':'#3d8adc',
            },
            {
                value: 80,
                'color':'#EEE',
                'label': 'Non Member',
            },
    ];
     */
    $html .= "<script>
	var pieData = $js
	var myPie = new Chart(document.getElementById('" . $id . "').getContext('2d')).Doughnut(pieData, {percentageInnerCutout : 60});
	</script>
	";

    return $html;
}

function chart_pie($id, $title = FALSE, $data, $array = FALSE)
{

    if (!defined("chart")) {
        add_to_head("<script src='" . INCLUDES . "charts/Chart.min.js'></script>\n");
        define("chart", TRUE);
    }

    // require - data['value'], ['color'], ['highlight'], ['label']
    $id = $id ? $id : '';

    if (!is_array($array)) {
        $title_class = '';
        $stats = '';
        $icon = '';
        $width = '80';
        $height = '80';
    } else {
        $stats = (array_key_exists('stats', $array)) && $array['stats'] ? $array['stats'] : '';
        $title_class = (array_key_exists('title_class', $array)) && $array['class'] ? $array['class'] : '';
        $icon = (array_key_exists('icon', $array)) && $array['icon'] ? $array['icon'] : '';
        $width = (array_key_exists('width', $array)) && isnum($array['width']) ? $array['width'] : '80';
        $height = (array_key_exists('height', $array)) && isnum($array['height']) ? $array['height'] : '80';
    }

    if (count($data) > 0) {
        $js = "[";
        foreach ($data as $key => $cdata) {
            $js .= "{";
            foreach ($cdata as $arr => $value) {
                $js .= (isnum($value)) ? "$arr:$value," : "$arr:'$value',";
            }
            $js .= "},";
        }
        $js .= "];";
    }

    $html = "<div class='chart-container text-center' style='width:" . $width . "px; margin: 0 auto;'>\n";
    $html .= "<div id='canvas-holder'>\n";
    $html .= "<canvas id='" . $id . "' width='" . $width . "' height='" . $height . "'/>\n";
    $html .= "</div>\n";
    $html .= "</div>\n";
    if ($stats || $title) {
        $html .= "<div class='text-center'>\n";
        $html .= ($stats) ? "<span class='strong text-dark'>" . $stats . "</span>" : '';
        $html .= ($title) ? "<div class='text-darker " . $title_class . "' >$title</div>\n" : '';
        $html .= "</div>\n";
    }

    /*
     * var pieData = [
            {
                value: 30,
                'color':'#3d8adc',
            },
            {
                value: 80,
                'color':'#EEE',
                'label': 'Non Member',
            },
    ];
     */
    $html .= "<script>
	var pieData = $js
	var myPie = new Chart(document.getElementById('" . $id . "').getContext('2d')).Pie(pieData);
	</script>
	";

    return $html;
}

function chart_bar($id, $title = FALSE, $data_title, $data, $array = FALSE)
{

    if (!defined("chart")) {
        add_to_head("<script src='" . INCLUDES . "charts/Chart.min.js'></script>\n");
        define("chart", TRUE);
    }
    // label - ['Jan', 'Feb', 'March'..],
    // require - data['label'], ['fillColor'], ['strokeColor'], ['highlightFill'], 'highlightStroke', data: [65, 59, 80, 81, 56... 12 months]
    $id = $id ? $id : '';

    if (!is_array($array)) {
        $title_class = '';
        $stats = '';
        $icon = '';
        $width = '450';
        $height = '450';
    } else {
        $stats = (array_key_exists('stats', $array)) && $array['stats'] ? $array['stats'] : '';
        $title_class = (array_key_exists('title_class', $array)) && $array['class'] ? $array['class'] : '';
        $icon = (array_key_exists('icon', $array)) && $array['icon'] ? $array['icon'] : '';
        $width = (array_key_exists('width', $array)) && isnum($array['width']) ? $array['width'] : '450';
        $height = (array_key_exists('height', $array)) && isnum($array['height']) ? $array['height'] : '450';
    }

    if (count($data) > 0) {
        $js = "{
		labels : [";
        foreach ($data_title as $key => $ldata) {
            $js .= "'" . $ldata . "',";
        }
        $js .= "],
		datasets: [
		";
        foreach ($data as $key => $cdata) {
            $js .= "{";
            foreach ($cdata as $arr => $value) {
                $js .= ($arr == 'data') ? "$arr:$value," : "$arr:'$value',";
            }
            $js .= "},";
        }
        $js .= "]};";
    }

    $html = "<div class='chart-container text-center' style='width:100%; margin: 0 auto;'>\n";
    $html .= "<div id='canvas-holder'>\n";
    $html .= "<canvas id='" . $id . "' width='" . $width . "' height='" . $height . "'/>\n";
    $html .= "</div>\n";
    $html .= "</div>\n";
    if ($stats || $title) {
        $html .= "<div class='text-center'>\n";
        $html .= ($stats) ? "<span class='strong text-dark'>" . $stats . "</span>" : '';
        $html .= ($title) ? "<div class='text-darker " . $title_class . "' >$title</div>\n" : '';
        $html .= "</div>\n";
    }

    /*
     * var pieData = [
            {
                value: 30,
                'color':'#3d8adc',
            },
            {
                value: 80,
                'color':'#EEE',
                'label': 'Non Member',
            },
    ];
     */
    $html .= "<script>
	var barData = $js
	var myBar = new Chart(document.getElementById('" . $id . "').getContext('2d')).Bar(barData);
	</script>
	";

    return $html;
}

function chart_line($id, $title = FALSE, $data_title, $data, $array = FALSE)
{

    if (!defined("chart")) {
        add_to_head("<script src='" . INCLUDES . "charts/Chart.min.js'></script>\n");
        define("chart", TRUE);
    }
    // label - ['Jan', 'Feb', 'March'..],
    // require - data['label'], ['fillColor'], ['strokeColor'], ['highlightFill'], 'highlightStroke', data: [65, 59, 80, 81, 56... 12 months]
    $id = $id ? $id : '';

    if (!is_array($array)) {
        $title_class = '';
        $stats = '';
        $icon = '';
        $width = '450';
        $height = '450';
    } else {
        $stats = (array_key_exists('stats', $array)) && $array['stats'] ? $array['stats'] : '';
        $title_class = (array_key_exists('title_class', $array)) && $array['class'] ? $array['class'] : '';
        $icon = (array_key_exists('icon', $array)) && $array['icon'] ? $array['icon'] : '';
        $width = (array_key_exists('width', $array)) && isnum($array['width']) ? $array['width'] : '450';
        $height = (array_key_exists('height', $array)) && isnum($array['height']) ? $array['height'] : '450';
    }

    if (count($data) > 0) {
        $js = "{
		labels : [";
        foreach ($data_title as $key => $ldata) {
            $js .= "'" . $ldata . "',";
        }
        $js .= "],
		datasets: [
		";
        foreach ($data as $key => $cdata) {
            $js .= "{";
            foreach ($cdata as $arr => $value) {
                $js .= ($arr == 'data') ? "$arr:$value," : "$arr:'$value',";
            }
            $js .= "},";
        }
        $js .= "]};";
    }

    $html = "<div class='chart-container text-center' style='width:100%; margin: 0 auto;'>\n";
    $html .= "<div id='canvas-holder'>\n";
    $html .= "<canvas id='" . $id . "' width='" . $width . "' height='" . $height . "'/>\n";
    $html .= "</div>\n";
    $html .= "</div>\n";
    if ($stats || $title) {
        $html .= "<div class='text-center'>\n";
        $html .= ($stats) ? "<span class='strong text-dark'>" . $stats . "</span>" : '';
        $html .= ($title) ? "<div class='text-darker " . $title_class . "' >$title</div>\n" : '';
        $html .= "</div>\n";
    }

    /*
     * var pieData = [
            {
                value: 30,
                'color':'#3d8adc',
            },
            {
                value: 80,
                'color':'#EEE',
                'label': 'Non Member',
            },
    ];
     */
    $html .= "<script>
	var lineData = $js
	var myBar = new Chart(document.getElementById('" . $id . "').getContext('2d')).Line(lineData);
	</script>
	";

    return $html;
}


function xchart($id, $primary, $secondary = FALSE, $array = FALSE)
{
    if (!defined("xcharts")) {
        define("xcharts", TRUE);
        add_to_head("<script src='" . INCLUDES . "charts/xcharts/d3.v3.min.js' charset='utf-8'></script>");
        add_to_head("<script src='" . INCLUDES . "charts/xcharts/xcharts.min.js'></script>\n");
        add_to_head("<link href='" . INCLUDES . "charts/xcharts/xcharts.css' rel='stylesheet' type='text/css' media='screen' />");
    }
    $vis_type = array("bar" => "bar", "cumulative" => "cumulative", "line" => "line", "line-dotted" => "line-dotted");
    $scale_type = array("0" => "ordinal", "1" => "linear", "2" => "time", "3" => "exponential");
    if (isset($array) && is_array($array)) {
        $axisPaddingLeft = (array_key_exists("axisPaddingLeft", $array)) ? $array['axisPaddingLeft'] : 20;
        $paddingLeft = (array_key_exists("paddingLeft", $array)) ? $array['paddingLeft'] : 30;
        $axisPaddingBottom = (array_key_exists("axisPaddingBottom", $array)) ? $array['axisPaddingBottom'] : 5;
        $paddingBottom = (array_key_exists("paddingBottom", $array)) ? $array['paddingBottom'] : 20;
        $hideY = (array_key_exists("hideY", $array)) ? add_to_head("<style>#$id .axisY { display:none; }</style>") : "";
        $hideX = (array_key_exists("hideX", $array)) ? add_to_head("<style>#$id .axisX { display:none; }</style>") : "";
        $height = (array_key_exists("height", $array)) ? $array['height'] : "200px";
        $x_scale_type = (array_key_exists("x_scale", $array)) ? $scale_type[$array['x_scale']] : $scale_type['0'];
        $y_scale_type = (array_key_exists("y_scale", $array)) ? $scale_type[$array['y_scale']] : $scale_type['1'];
        $type = (array_key_exists("type", $array)) ? $vis_type[$array['type']] : $vis_type['bar'];
    } else {
        $axisPaddingLeft = 20;
        $paddingLeft = 0;
        $axisPaddingBottom = 5;
        $paddingBottom = 20;
        $hideY = "";
        $hideX = "";
        $height = "200px";
        $x_scale_type = $scale_type['0'];
        $y_scale_type = $scale_type['1'];
        $type = $vis_type['bar'];
    }
    $html = "";
    $html .= "<figure style='width:100%; min-height:$height' id='$id'></figure>";
    // demo data
    //new xChart('bar', {"xScale":"ordinal","yScale":"linear","type":"bar",
    // "main":[{"className":".pizza","data":[{"x":"Pepperoni","y":12},{"x":"Cheese","y":8}]}],
    // "comp":[{"className":".pizza","type":"line-dotted","data":[{"x":"Pepperoni","y":10},{"x":"Cheese","y":4}]}]},
    //'#pizza');
    //    'type': '$type',
    $data = " {
    'xScale': '$x_scale_type',
    'yScale': '$y_scale_type',

    'main':";
    $data .= $primary;
    if ($secondary !== "") {
        $data .= ",";
        $data .= "'comp':";
        $data .= $secondary;
    }
    $data .= "}";
    $opts = "{
    paddingLeft : $paddingLeft ,
    axisPaddingLeft : $axisPaddingLeft,
    axisPaddingBottom : $axisPaddingBottom,
    paddingBottom : $paddingBottom
    }";
    $html .= add_to_jquery("
    var $id = new xChart('$type', $data, '#$id', $opts);
    ");

    return $html;
    //return print_p($secondary);
    //return print_p($data);
}

function chart_data($x, $y, $type = FALSE, $class = FALSE)
{
    $null_x = array("0" => "No Data");
    $null_y = array("0" => "0");
    if (is_array($x)) {
        $rel_x = (in_array(" ", $x)) ? $null_x : $x;
    } else {
        $rel_x = $null_x;
    }
    if (is_array($y)) {
        $rel_y = (in_array(" ", $y)) ? $null_y : $y;
    } else {
        $rel_y = $null_y;
    }
    $class = ($class == "") ? "pizza" : $class;
    $type = ($type !== "") ? $type : "bar";
    $chdata = "[{ 'className' : '.$class',";
    $chdata .= "'type' : '$type',";
    $chdata .= "'data': [";
    $counter = count($rel_x);
    foreach ($rel_x as $arr => $v) {
        $x_value = $v;
        $y_value = $rel_y[$arr];
        $coma = ($arr == $counter - 1) ? "" : ",";
        $chdata .= "{'x': '$x_value', 'y': $y_value }$coma";
    }
    $chdata .= "]";
    $chdata .= "}]";

    return $chdata;
    //return print_p($chdata);
}

?>