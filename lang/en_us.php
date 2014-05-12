<?php

$L = array();

// required fields
$L["module_name"] = "Arbitrary Settings";
$L["module_description"] = "This simple module adds a section to the bottom of the Settings -> Main tab containing whatever additional settings you want. These settings can be accessed and used outside of Form Tools through custom PHP calls.";
$L["word_orientation"] = "Orientation";
$L["word_labels"] = "Labels";

$L["phrase_add_setting"] = "Add Setting";
$L["phrase_field_label"] = "Field Label";
$L["phrase_field_type"] = "Field Type";
$L["phrase_setting_label"] = "Setting Label";
$L["phrase_edit_setting"] = "Edit Setting";
$L["phrase_how_to_use"] = "How to Use";
$L["phrase_settings_title"] = "Settings Title";
$L["phrase_setting_identifier"] = "Setting Identifier";

$L["validation_no_setting_label"] = "Please enter the setting label.";
$L["validation_no_field_type"] = "Please enter the field type.";
$L["validation_no_setting_identifier"] = "Please enter the setting identifier.";
$L["validation_invalid_setting_identifier"] = "The setting identifier field may only contain alphanumeric (a-Z) cthe underscore character.";
$L["validation_setting_identifier_conflict"] = "Please enter a setting identifier other than <b>settings_title</b>. That's reserved for a module setting.";

$L["notify_problem_installing"] = "There following error occurred when trying to create the database tables for this module: <b>{\$error}</b>";
$L["notify_no_settings"] = "There are no settings defined in this module. Use the button below to create one.";
$L["notify_setting_added"] = "The setting has been added.";
$L["notify_setting_deleted"] = "The setting has been deleted.";
$L["notify_setting_updated"] = "The setting has been updated.";
$L["notify_settings_order_updated"] = "The settings order has been updated.";
$L["notify_setting_title_updated"] = "The setting title has been updated.";

$L["confirm_delete_setting"] = "Are you sure you want to delete this setting?";

$L["text_settings_title_hint"] = "This determines the title that appears above your settings on the Settings -> Main tab.";
$L["text_setting_identifier_hint"] = "This setting is used to uniquely identify the setting and extract whatever value is stored in the field. It must be alphanumeric only.";
$L["text_how_to_use1"] = "To access the values stored in your settings, you will need to use the following PHP code.";
$L["text_how_to_use2"] = "<b>\$settings</b> will be a hash (associative array) containing all the values. To access them, you will need to use <b>\$settings[\"your_setting_identifier\"]</b>.";