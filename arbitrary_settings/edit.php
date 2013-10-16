<?php

require("../../global/library.php");
ft_init_module_page();
$sid = ft_load_module_field("arbitrary_settings", "sid", "sid");

if (isset($_POST["update"]))
  list($g_success, $g_message) = as_update_setting($sid, $_POST);

$setting_info = as_get_setting($sid);
$num_options = count($setting_info["options"]);

// ------------------------------------------------------------------------------------------------

$page_vars = array();
$page_vars["head_title"] = $L["phrase_edit_setting"];
$page_vars["head_string"] = "<script type=\"text/javascript\" src=\"global/scripts/field_options.js\"></script>";
$page_vars["setting_info"] = $setting_info;
$page_vars["js_messages"] = array("word_delete");
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


ft_display_module_page("templates/edit.tpl", $page_vars);
