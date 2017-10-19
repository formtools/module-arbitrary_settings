<?php


namespace FormTools\Modules\ArbitrarySettings;

use FormTools\Core;
use FormTools\General;
use FormTools\Hooks;
use FormTools\Module as FormToolsModule;
use FormTools\Settings;
use PDOException;


class Module extends FormToolsModule
{
    protected $moduleName = "Arbitrary Settings";
    protected $moduleDesc = "This simple module adds a section to the bottom of the Settings -> Main tab containing whatever additional settings you want. These settings can be accessed and used outside of Form Tools through custom PHP calls.";
    protected $author = "Ben Keen";
    protected $authorEmail = "ben.keen@gmail.com";
    protected $authorLink = "https://formtools.org";
    protected $version = "2.0.0";
    protected $date = "2017-10-18";
    protected $originLanguage = "en_us";
    protected $jsFiles = array(
        "{MODULEROOT}/scripts/field_options.js"
    );

    protected $nav = array(
        "module_name"       => array("index.php", false),
        "word_labels"       => array("labels.php", false),
        "phrase_how_to_use" => array("how_to_use.php", false)
    );

    /**
     * The installation script for the module.
     */
    public function install($module_id)
    {
        $db = Core::$db;

        $success = true;
        $message = "";
        try {
            $db->beginTransaction();
            $db->query("
                CREATE TABLE {PREFIX}module_arbitrary_settings (
                    sid mediumint(8) unsigned NOT NULL auto_increment,
                    setting_label varchar(255) NOT NULL,
                    setting_identifier varchar(100) NOT NULL,
                    field_type enum('textbox','textarea','password','radios','checkboxes','select','multi-select') NOT NULL,
                    field_orientation enum('horizontal','vertical','na') NOT NULL default 'na',
                    setting_order smallint(6) NOT NULL,
                    PRIMARY KEY  (sid)
                  ) DEFAULT CHARSET=utf8
            ");
            $db->execute();

            $db->query("
                CREATE TABLE {PREFIX}module_arbitrary_setting_options (
                    sid mediumint(9) NOT NULL,
                    option_text varchar(255) default NULL,
                    field_order smallint(6) NOT NULL,
                    PRIMARY KEY (sid, field_order)
                  ) DEFAULT CHARSET=utf8
            ");
            $db->execute();

            Settings::set(array("settings_title" => "ARBITRARY SETTINGS"), "arbitrary_settings");

            $db->processTransaction();
        } catch (PDOException $e) {
            $db->rollbackTransaction();
            $L = $this->getLangStrings();
            $success = false;
            $message = General::evalSmartyString($L["notify_problem_installing"], array("error" => $e->getMessage()));
        }

        $this->resetHooks();

        return array($success, $message);
    }


    /**
     * The uninstallation script for the module.
     *
     * @return array [0] T/F, [1] success message
     */
    public function uninstall($module_id)
    {
        $db = Core::$db;

        $db->query("DROP TABLE {PREFIX}module_arbitrary_settings");
        $db->execute();

        $db->query("DROP TABLE {PREFIX}module_arbitrary_setting_options");
        $db->execute();

        $db->query("DELETE FROM {PREFIX}settings WHERE module = 'arbitrary_settings'");
        $db->execute();

        return array(true, "");
    }


    public function resetHooks()
    {
        Hooks::unregisterModuleHooks("arbitrary_settings");
        Hooks::registerHook("template", "arbitrary_settings", "admin_settings_main_tab_bottom", "", "displaySettings");
        Hooks::registerHook("code", "arbitrary_settings", "end", "FormTools\\Settings::updateMainSettings", "saveSettings");
    }

    /**
     * This function is attached to the admin_edit_view_client_map_filter_dropdown hook. It populates the
     * Edit View -> Client Map Filters -> client fields dropdown with any additional fields defined in this module.
     */
    public function displayExtendedFieldOptions($location, $template_vars)
    {
        $L = $this->getLangStrings();
        $client_fields = Fields::getSettings(1, "all");

        if ($client_fields["num_results"] == 0) {
            return;
        }

        $current_row        = $template_vars["count"];
        $client_map_filters = $template_vars["client_map_filters"];
        $selected_value     = $client_map_filters[$current_row-1]["filter_values"];

        echo "<optgroup label=\"{$L["module_name"]}\">\n";
        foreach ($client_fields["results"] as $field_info) {
            $field_label = htmlspecialchars($field_info["field_label"]);
            $selected    = ($selected_value == "as_" . $field_info["sid"]) ? "selected" : "";
            echo "<option value=\"as_{$field_info["sid"]}\" {$selected}>{$field_label}</option>\n";
        }
        echo "</optgroup>\n";
    }

}

