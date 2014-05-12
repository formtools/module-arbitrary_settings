<?php


/**
 * The installation script for the module.
 */
function arbitrary_settings__install($module_id)
{
  global $g_table_prefix, $LANG;

  $queries = array();
  $queries[] = "CREATE TABLE {$g_table_prefix}module_arbitrary_settings (
    sid mediumint(8) unsigned NOT NULL auto_increment,
    setting_label varchar(255) NOT NULL,
    setting_identifier varchar(100) NOT NULL,
    field_type enum('textbox','textarea','password','radios','checkboxes','select','multi-select') NOT NULL,
    field_orientation enum('horizontal','vertical','na') NOT NULL default 'na',
    setting_order smallint(6) NOT NULL,
    PRIMARY KEY  (sid)
  ) DEFAULT CHARSET=utf8";

  $queries[] = "CREATE TABLE {$g_table_prefix}module_arbitrary_setting_options (
    sid mediumint(9) NOT NULL,
    option_text varchar(255) default NULL,
    field_order smallint(6) NOT NULL,
    PRIMARY KEY (sid, field_order)
  ) DEFAULT CHARSET=utf8";

  $has_problem = false;
  foreach ($queries as $query)
  {
    $result = @mysql_query($query);
    if (!$result)
    {
      $has_problem = true;
      break;
    }
  }

  ft_set_settings(array("settings_title" => "ARBITRARY SETTINGS"), "arbitrary_settings");

  // if there was a problem, remove all the table and return an error
  $success = true;
  $message = "";
  if ($has_problem)
  {
    $success = false;
    @mysql_query("DROP TABLE {$g_table_prefix}module_arbitrary_settings");
    @mysql_query("DROP TABLE {$g_table_prefix}module_arbitrary_setting_options");
    $mysql_error = mysql_error();
    $message     = ft_eval_smarty_string($LANG["arbitrary_settings"]["notify_problem_installing"], array("error" => $mysql_error));
  }

  as_reset_hooks();

  return array($success, $message);
}


/**
 * The uninstallation script for the module.
 *
 * @return array [0] T/F, [1] success message
 */
function arbitrary_settings__uninstall($module_id)
{
  global $g_table_prefix, $LANG;

  $result = mysql_query("DROP TABLE {$g_table_prefix}module_arbitrary_settings");
  $result = mysql_query("DROP TABLE {$g_table_prefix}module_arbitrary_setting_options");
  mysql_query("DELETE FROM {$g_table_prefix}settings WHERE module = 'arbitrary_settings'");

  return array(true, "");
}

