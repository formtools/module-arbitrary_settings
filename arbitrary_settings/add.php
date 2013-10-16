<?php

require("../../global/library.php");
ft_init_module_page();

if (isset($_POST["add"]))
  list($g_success, $g_message) = as_add_field($_POST);

$page_vars = array();
$page_vars["head_title"] = $L["phrase_add_setting"];
$page_vars["head_string"] = "<script src=\"global/scripts/field_options.js\"></script>";
$page_vars["js_messages"] = array("word_delete");
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

ft_display_module_page("templates/add.tpl", $page_vars);
