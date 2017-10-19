<?php

require_once("../../global/library.php");

use FormTools\Core;
use FormTools\Modules;

$module = Modules::initModulePage("admin");
$L = $module->getLangStrings();
$root_url = Core::getRootUrl();

$page_vars = array(
    "head_title" => $L["module_name"],
    "css_files" => array(
        "$root_url/global/codemirror/lib/codemirror.css",
    ),
    "js_files" => array(
        "$root_url/global/codemirror/lib/codemirror.js",
        "$root_url/global/codemirror/mode/xml/xml.js",
        "$root_url/global/codemirror/mode/smarty/smarty.js",
        "$root_url/global/codemirror/mode/php/php.js",
        "$root_url/global/codemirror/mode/htmlmixed/htmlmixed.js",
        "$root_url/global/codemirror/mode/css/css.js",
        "$root_url/global/codemirror/mode/javascript/javascript.js",
        "$root_url/global/codemirror/mode/clike/clike.js"
    )
);

$module->displayPage("templates/how_to_use.tpl", $page_vars);
