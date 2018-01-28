<?php

require_once("../../global/library.php");

use FormTools\Modules;
use FormTools\Settings;

$module = Modules::initModulePage("admin");
$L = $module->getLangStrings();

$success = true;
$message = "";
if (isset($_POST["update"])) {
    Settings::set(array("settings_title" => $_POST["settings_title"]), "arbitrary_settings");
    $success = true;
    $message = $L["notify_setting_title_updated"];
}

$page_vars = array(
    "g_success" => $success,
    "g_message" => $message,
    "head_title" => $L["module_name"],
    "settings" => $module->getSettings()
);

$module->displayPage("templates/labels.tpl", $page_vars);
