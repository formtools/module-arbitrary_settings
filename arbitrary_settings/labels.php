<?php

require_once("../../global/library.php");
ft_init_module_page();
$folder = dirname(__FILE__);
require_once("$folder/library.php");

if (isset($_POST["update"]))
{
	ft_set_settings(array("settings_title" => $_POST["settings_title"]), "arbitrary_settings");
	$g_success = true;
	$g_message = $L["notify_setting_title_updated"];
}
$settings = ft_get_module_settings();

// ------------------------------------------------------------------------------------------------

$page_vars = array();
$page_vars["head_title"] = $L["module_name"];
$page_vars["settings"] = $settings;

ft_display_module_page("templates/labels.tpl", $page_vars);
