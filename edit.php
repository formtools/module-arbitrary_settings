<?php

require("../../global/library.php");

use FormTools\Modules;
use FormTools\Modules\ArbitrarySettings\Fields;

$module = Modules::initModulePage("admin");
$L = $module->getLangStrings();

$sid = Modules::loadModuleField("arbitrary_settings", "sid", "sid");

$success = true;
$message = "";
if (isset($_POST["update"])) {
    list($success, $message) = Fields::updateSetting($sid, $_POST, $L);
}
$setting_info = Fields::getSetting($sid);
$num_options = count($setting_info["options"]);

$page_vars = array(
    "g_success" => $success,
    "g_message" => $message,
    "head_title" => $L["phrase_edit_setting"],
    "setting_info" => $setting_info,
    "js_messages" => array("word_delete")
);

$page_vars["head_js"] =<<< EOF
var rules = [];
rules.push("required,setting_label,{$L["validation_no_setting_label"]}");
rules.push("required,setting_identifier,{$L["validation_no_setting_identifier"]}");
rules.push("is_alpha,setting_identifier,{$L["validation_invalid_setting_identifier"]}");
rules.push("if:setting_identifier=settings_title,required,placeholder,{$L["validation_setting_identifier_conflict"]}");
rules.push("required,field_type,{$L["validation_no_field_type"]}");
$(function() { as_ns.num_rows = $num_options; });

var page_ns = {};
page_ns.delete_field = function(sid) {
  if (confirm("{$L["confirm_delete_setting"]}")) {
    window.location = 'index.php?delete=' + sid;
  }
}
EOF;


$module->displayPage("templates/edit.tpl", $page_vars);
