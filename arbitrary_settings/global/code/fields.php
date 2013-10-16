<?php


/**
 * Adds a new field to the database.
 */
function as_add_field($info)
{
  global $g_table_prefix, $L;

  $info = ft_sanitize($info);

  // get the next field order
  $query = mysql_query("SELECT setting_order FROM {$g_table_prefix}module_arbitrary_settings ORDER BY setting_order DESC LIMIT 1");
  $result = mysql_fetch_assoc($query);

  $next_order = 1;
  if (!empty($result))
    $next_order = $result["setting_order"] + 1;

  $is_required = (isset($info["is_required"])) ? "yes" : "no";

  // add the main record first
  $query = mysql_query("
    INSERT INTO {$g_table_prefix}module_arbitrary_settings (setting_label, setting_identifier, field_type,
       field_orientation, setting_order)
    VALUES ('{$info["setting_label"]}', '{$info["setting_identifier"]}', '{$info["field_type"]}',
      '{$info["field_orientation"]}', $next_order)
      ");

  $sid = mysql_insert_id();

  // if this field had multiple options, add them too
  if ($info["field_type"] == "select" || $info["field_type"] == "multi-select" ||
      $info["field_type"] == "radios" || $info["field_type"] == "checkboxes")
  {
    for ($i=1; $i<=$info["num_rows"]; $i++)
    {
      if (!isset($info["field_option_text_$i"]) || empty($info["field_option_text_$i"]))
        continue;

      $option_text = $info["field_option_text_$i"];

      mysql_query("
        INSERT INTO {$g_table_prefix}module_arbitrary_setting_options (sid, option_text, field_order)
        VALUES ($sid, '$option_text', $i)
          ");
    }
  }

  $message = ft_eval_smarty_string($L["notify_setting_added"], array("sid" => $sid));
  return array(true, $message);
}


/**
 * Updates an extended field in the database.
 */
function as_update_setting($sid, $info)
{
  global $g_table_prefix, $L;

  $info = ft_sanitize($info);
  $is_required = (isset($info["is_required"])) ? "yes" : "no";

  $result = mysql_query("
    UPDATE {$g_table_prefix}module_arbitrary_settings
    SET     setting_label = '{$info["setting_label"]}',
            setting_identifier = '{$info["setting_identifier"]}',
            field_type = '{$info["field_type"]}',
            field_orientation = '{$info["field_orientation"]}'
    WHERE   sid = $sid
      ");

  if (!$result)
    return array(false, $L["notify_field_not_updated"] . mysql_error());

  @mysql_query("DELETE FROM {$g_table_prefix}module_arbitrary_setting_options WHERE sid = $sid");

  // if this field had multiple options, add them too
  if ($info["field_type"] == "select" || $info["field_type"] == "multi-select" ||
      $info["field_type"] == "radios" || $info["field_type"] == "checkboxes")
  {
    for ($i=1; $i<=$info["num_rows"]; $i++)
    {
      if (!isset($info["field_option_text_$i"]) || empty($info["field_option_text_$i"]))
        continue;

      $option_text = $info["field_option_text_$i"];
      @mysql_query("
        INSERT INTO {$g_table_prefix}module_arbitrary_setting_options
          (sid, option_text, field_order)
        VALUES ($sid, '$option_text', $i)
          ");
    }
  }

  return array(true, $L["notify_setting_updated"]);
}


/**
 * Returns a page (or all) client fields.
 *
 * @param integer $page_num
 * @param array $search a hash whose keys correspond to database column names
 * @return array
 */
function as_get_settings($page_num = 1, $num_per_page = 10, $search = array())
{
  global $g_table_prefix;

  $where_clause = "";
  if (!empty($search))
  {
    $clauses = array();
    while (list($key, $value) = each($search))
      $clauses[] = "$key = '$value'";

    if (!empty($clauses))
      $where_clause = "WHERE " . join(" AND ", $clauses);
  }

  if ($num_per_page == "all")
  {
    $query = mysql_query("
      SELECT sid
      FROM   {$g_table_prefix}module_arbitrary_settings
      $where_clause
      ORDER BY setting_order
        ");
  }
  else
  {
    // determine the offset
    if (empty($page_num)) { $page_num = 1; }
    $first_item = ($page_num - 1) * $num_per_page;

    $query = mysql_query("
      SELECT sid
      FROM   {$g_table_prefix}module_arbitrary_settings
      $where_clause
      ORDER BY setting_order
      LIMIT $first_item, $num_per_page
        ");
  }

  $count_query = mysql_query("SELECT count(*) as c FROM {$g_table_prefix}module_arbitrary_settings");
  $count_hash = mysql_fetch_assoc($count_query);
  $num_results = $count_hash["c"];

  $infohash = array();
  while ($field = mysql_fetch_assoc($query))
  {
    $sid = $field["sid"];
    $infohash[] = as_get_setting($sid);
  }

  $return_hash["results"] = $infohash;
  $return_hash["num_results"] = $num_results;

  return $return_hash;
}


/**
 * Deletes an extended client field. This also has various ramifications throughout the
 * rest of the script, so it tidies up them all. Namely:
 *  -- it deletes any data added for clients in the removed field
 *  -- it deletes any Client Map View filters that map to this field
 */
function as_delete_setting($sid)
{
  global $g_table_prefix, $L;

  mysql_query("DELETE FROM {$g_table_prefix}module_arbitrary_setting_options WHERE sid = $sid");
  mysql_query("DELETE FROM {$g_table_prefix}module_arbitrary_settings WHERE sid = $sid");

  as_reorder_settings();

  return array(true, $L["notify_setting_deleted"]);
}


/**
 * Returns all information about a field.
 */
function as_get_setting($sid)
{
  global $g_table_prefix;

  $query = mysql_query("
    SELECT *
    FROM   {$g_table_prefix}module_arbitrary_settings
    WHERE  sid = $sid
      ");
  $info = mysql_fetch_assoc($query);
  $info["options"] = array();

  if ($info["field_type"] == "select" || $info["field_type"] == "multi-select" ||
      $info["field_type"] == "radios" || $info["field_type"] == "checkboxes")
  {
    $query = mysql_query("
      SELECT *
      FROM   {$g_table_prefix}module_arbitrary_setting_options
      WHERE  sid = $sid
      ORDER BY field_order ASC
        ");

    $options = array();
    while ($row = mysql_fetch_assoc($query))
      $options[] = $row;

    $info["options"] = $options;
  }

  return $info;
}


/**
 * This function handles the actual field generation for the form.
 */
function as_display_settings($location, $template_vars)
{
  global $g_root_dir;

  // okay! We have some stuff to show. Grab the section title
  $module_settings = ft_get_module_settings("", "arbitrary_settings");

  $smarty = new Smarty();
  $smarty->template_dir = "$g_root_dir/modules/arbitrary_settings/smarty/";
  $smarty->compile_dir  = "$g_root_dir/themes/default/cache/";

  $settings = as_get_settings(1, "all");
  if (count($settings["results"]) == 0) {
  	return;
  }

  $results = array();
  foreach ($settings["results"] as $setting_info)
  {
  	$setting_identifier = $setting_info["setting_identifier"];
  	if (isset($module_settings[$setting_identifier]))
  	{
  		$setting_info["content"] = $module_settings[$setting_identifier];
  		if (in_array($setting_info["field_type"], array("checkboxes", "multi-select")))
  		{
        $setting_info["content"] = explode("|", $module_settings[$setting_identifier]);
  		}
  	}
  	$results[] = $setting_info;
  }

  $smarty->assign("title", $module_settings["settings_title"]);
  $smarty->assign("settings", $results);

  $output = $smarty->fetch("$g_root_dir/modules/arbitrary_settings/smarty/section_html.tpl");

  echo $output;
}


/**
 * Called on the main fields page. This updates the orders of the entire list of
 * Extended Client Fields. Note: the option to sort the Fields only appears if there is
 * 2 or more fields.
 *
 * @param array $info the form contents
 * @return array Returns array with indexes:<br/>
 *               [0]: true/false (success / failure)<br/>
 *               [1]: message string<br/>
 */
function as_update_settings_order($info)
{
  global $g_table_prefix, $L;

  // loop through all the fields in $info that are being re-sorted and compile a list of
  // view_id => order pairs.
  $new_field_orders = array();
  foreach ($info as $key => $value)
  {
    if (preg_match("/^setting_(\d+)_order$/", $key, $match))
    {
      $sid = $match[1];
      $new_field_orders[$sid] = $value;
    }
  }

  // okay! Since we may have only updated a *subset* of all fields (the fields page is
  // arranged in pages), get a list of ALL extended client fields, add them to
  // $new_field_orders and sort the entire lot of them in one go
  $view_info = array();
  $query = mysql_query("
    SELECT sid, setting_order
    FROM   {$g_table_prefix}module_arbitrary_settings
      ");
  while ($row = mysql_fetch_assoc($query))
  {
    if (!array_key_exists($row["sid"], $new_field_orders))
      $new_field_orders[$row["sid"]] = $row["setting_order"];
  }

  // sort by the ORDER (the value - non-key - of the hash)
  asort($new_field_orders);

  $count = 1;
  foreach ($new_field_orders as $sid => $order)
  {
    mysql_query("
      UPDATE {$g_table_prefix}module_arbitrary_settings
      SET	   setting_order = $count
      WHERE  sid = $sid
        ");
    $count++;
  }

  // return success
  return array(true, $L["notify_settings_order_updated"]);
}


/**
 * This function is attached to the admin_edit_view_client_map_filter_dropdown hook. It populates the
 * Edit View -> Client Map Filters -> client fields dropdown with any additional fields defined in this module.
 */
function as_display_extended_field_options($location, $template_vars)
{
  global $g_table_prefix, $LANG;
  ft_include_module("extended_client_fields");

  $client_fields = as_get_settings(1, "all");

  if ($client_fields["num_results"] == 0)
    return;

  $current_row        = $template_vars["count"];
  $client_map_filters = $template_vars["client_map_filters"];
  $selected_value     = $client_map_filters[$current_row-1]["filter_values"];

  echo "<optgroup label=\"{$LANG["extended_client_fields"]["module_name"]}\">\n";
  foreach ($client_fields["results"] as $field_info)
  {
    $field_label = htmlspecialchars($field_info["field_label"]);
    $selected    = ($selected_value == "as_" . $field_info["sid"]) ? "selected" : "";
    echo "<option value=\"as_{$field_info["sid"]}\" {$selected}>{$field_label}</option>\n";
  }

  echo "</optgroup>\n";
}


/**
 * Called whenever a user deletes a field. This updates the field order.
 */
function as_reorder_settings()
{
  global $g_table_prefix;

  $result = mysql_query("SELECT sid FROM {$g_table_prefix}module_arbitrary_settings ORDER BY setting_order ASC");

  $order = 1;
  while ($row = mysql_fetch_assoc($result))
  {
    $sid = $row["sid"];
    mysql_query("
      UPDATE {$g_table_prefix}module_arbitrary_settings
      SET    setting_order = $order
      WHERE  sid = $sid
        ");
    $order++;
  }
}

