<?php


function as_reset_hooks()
{
  ft_unregister_module_hooks("arbitrary_settings");
	ft_register_hook("template", "arbitrary_settings", "admin_settings_main_tab_bottom", "", "as_display_settings");
  ft_register_hook("code", "arbitrary_settings", "end", "ft_update_main_settings", "as_save_settings");
}



/**
 * This is called whenever the administrator updates the main settings tab.
 *
 * @param array $vars
 */
function as_save_settings($vars)
{
	$settings = as_get_settings(1, "all");

	$settings_to_update = array();
	foreach ($settings["results"] as $setting_info)
	{
    $sid                = $setting_info["sid"];
    $setting_identifier = $setting_info["setting_identifier"];

    if (!isset($_POST["arbitrary_setting_{$sid}"]))
      continue;

    $value = $_POST["arbitrary_setting_{$sid}"];
    if (is_array($_POST["arbitrary_setting_{$sid}"]))
    {
      $value = implode("|", $_POST["arbitrary_setting_{$sid}"]);
    }

    $settings_to_update[$setting_identifier] = $value;
	}

	if (!empty($settings_to_update))
	{
    ft_set_settings($settings_to_update, "arbitrary_settings");
	}
}

