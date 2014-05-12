<?php

require_once("../../global/library.php");
ft_init_module_page();

$folder = dirname(__FILE__);
require_once("$folder/library.php");

if (isset($_GET["delete"]))
  list($g_success, $g_message) = as_delete_setting($_GET["delete"]);

if (isset($_POST["add_field"]))
{
  header("location: add.php");
  exit;
}
else if (isset($_POST["update_order"]))
{
  list($g_success, $g_message) = as_update_settings_order($_POST);
}

$num_fields_per_page = 10;

$page = ft_load_module_field("extended_client_fields", "page", "extended_client_fields_page", 1);
$info = as_get_settings($page);
$results     = $info["results"];
$num_results = $info["num_results"];

// ------------------------------------------------------------------------------------------------

$page_vars = array();
$page_vars["results"] = $results;
$page_vars["head_title"] = $L["module_name"];
$page_vars["pagination"] = ft_get_page_nav($num_results, $num_fields_per_page, $page, "");
$page_vars["js_messages"] = array("word_edit");
$page_vars["head_js"] =<<< EOF
var page_ns = {};
page_ns.delete_dialog = $("<div></div>");
page_ns.delete_field = function(client_field_id) {
  ft.create_dialog({
    dialog:      page_ns.delete_dialog,
    title:      "{$LANG["phrase_please_confirm"]}",
    content:    "{$L["confirm_delete_setting"]}",
    popup_type: "warning",
    buttons: [{
      text: "{$LANG["word_yes"]}",
      click: function() {
        window.location = 'index.php?delete=' + client_field_id;
      }
    },
    {
      text: "{$LANG["word_no"]}",
      click: function() {
        $(this).dialog("close");
      }
    }]
  });
}
EOF;

ft_display_module_page("templates/index.tpl", $page_vars);
