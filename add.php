<?php

require("../../global/library.php");

use FormTools\Modules;
use FormTools\Modules\ArbitrarySettings\Fields;

$module = Modules::initModulePage("admin");
$L = $module->getLangStrings();

$success = true;
$message = "";
if (isset($_POST["add"])) {
    list($success, $message) = Fields::addField($_POST, $L);
}

$page_vars = array(
    "g_success" => $success,
    "g_message" => $message,
    "head_title" => $L["phrase_add_setting"],
    "js_messages" => array("word_delete")
);

$page_vars["head_js"] =<<< EOF
var rules = [];
rules.push("required,setting_label,{$L["validation_no_setting_label"]}");
rules.push("required,setting_identifier,{$L["validation_no_setting_identifier"]}");
rules.push("is_alpha,setting_identifier,{$L["validation_invalid_setting_identifier"]}");
rules.push("if:setting_identifier=settings_title,required,placeholder,{$L["validation_setting_identifier_conflict"]}");
rules.push("required,field_type,{$L["validation_no_field_type"]}");
$(function() {
  as_ns.add_field_option(null, null);
  as_ns.add_field_option(null, null);
  $("#field_type").val("").bind("change keyup", function() {
    as_ns.change_field_type(this.value);
  });
});
EOF;

$module->displayPage("templates/add.tpl", $page_vars);
