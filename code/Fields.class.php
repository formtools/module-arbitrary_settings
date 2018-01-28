<?php


namespace FormTools\Modules\ArbitrarySettings;

use FormTools\Core;
use FormTools\General;
use PDO, Exception;


class Fields
{
    /**
     * Adds a new field to the database.
     */
    public static function addField($info, $L)
    {
        $db = Core::$db;

        // get the next field order
        $next_order = self::getNextSettingId();

        $db->query("
            INSERT INTO {PREFIX}module_arbitrary_settings (setting_label, setting_identifier, field_type, field_orientation, setting_order)
            VALUES (:setting_label, :setting_identifier, :field_type, :field_orientation, :setting_order)
        ");
        $db->bindAll(array(
            "setting_label" => $info["setting_label"],
            "setting_identifier" => $info["setting_identifier"],
            "field_type" => $info["field_type"],
            "field_orientation" => $info["field_orientation"],
            "setting_order" => $next_order
        ));
        $db->execute();

        $sid = $db->getInsertId();

        // if this field had multiple options, add them too
        if ($info["field_type"] == "select" || $info["field_type"] == "multi-select" ||
            $info["field_type"] == "radios" || $info["field_type"] == "checkboxes") {
            for ($i=1; $i<=$info["num_rows"]; $i++) {
                if (!isset($info["field_option_text_$i"]) || empty($info["field_option_text_$i"])) {
                    continue;
                }
                $option_text = $info["field_option_text_$i"];

                $db->query("
                    INSERT INTO {PREFIX}module_arbitrary_setting_options (sid, option_text, field_order)
                    VALUES (:sid, :option_text, :field_order)
                ");
                $db->bindAll(array(
                    "sid" => $sid,
                    "option_text" => $option_text,
                    "field_order" => $i
                ));
                $db->execute();
            }
        }

        $message = General::evalSmartyString($L["notify_setting_added"], array("sid" => $sid));

        return array(true, $message);
    }


    /**
     * Updates an extended field in the database.
     */
    public static function updateSetting($sid, $info, $L)
    {
        $db = Core::$db;

        try {
            $db->query("
                UPDATE {PREFIX}module_arbitrary_settings
                SET     setting_label = :setting_label,
                        setting_identifier = :setting_identifier,
                        field_type = :field_type,
                        field_orientation = :field_orientation
                WHERE   sid = :sid
            ");
            $db->bindAll(array(
                "setting_label" => $info["setting_label"],
                "setting_identifier" => $info["setting_identifier"],
                "field_type" => $info["field_type"],
                "field_orientation" => $info["field_orientation"],
                "sid" => $sid
            ));
            $db->execute();

            $db->query("DELETE FROM {PREFIX}module_arbitrary_setting_options WHERE sid = :sid");
            $db->bind("sid", $sid);
            $db->execute();

            // if this field had multiple options, add them too
            if ($info["field_type"] == "select" || $info["field_type"] == "multi-select" ||
                $info["field_type"] == "radios" || $info["field_type"] == "checkboxes") {
                for ($i=1; $i<=$info["num_rows"]; $i++) {
                    if (!isset($info["field_option_text_$i"]) || empty($info["field_option_text_$i"])) {
                        continue;
                    }

                    $option_text = $info["field_option_text_$i"];
                    $db->query("
                        INSERT INTO {PREFIX}module_arbitrary_setting_options (sid, option_text, field_order)
                        VALUES (:sid, :option_text, :field_order)
                    ");
                    $db->bindAll(array(
                        "sid" => $sid,
                        "option_text" => $option_text,
                        "field_order" => $i
                    ));
                    $db->execute();
                }
            }
        } catch (Exception $e) {
            return array(false, $L["notify_field_not_updated"] . $e->getMessage());
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
    public static function getSettings($page_num = 1, $num_per_page = 10, $search = array())
    {
        $db = Core::$db;

        $where_clause = "";
        if (!empty($search)) {
            $clauses = array();
            while (list($key, $value) = each($search)) {
                $clauses[] = "$key = '$value'";
            }
            if (!empty($clauses)) {
                $where_clause = "WHERE " . join(" AND ", $clauses);
            }
        }

        if ($num_per_page == "all") {
            $db->query("
                SELECT sid
                FROM   {PREFIX}module_arbitrary_settings
                $where_clause
                ORDER BY setting_order
            ");
        } else {
            // determine the offset
            if (empty($page_num)) {
                $page_num = 1;
            }
            $first_item = ($page_num - 1) * $num_per_page;

            $db->query("
                SELECT sid
                FROM   {PREFIX}module_arbitrary_settings
                $where_clause
                ORDER BY setting_order
                LIMIT $first_item, $num_per_page
            ");
        }

        $db->execute();
        $results = $db->fetchAll();

        $infohash = array();
        foreach ($results as $field) {
            $sid = $field["sid"];
            $infohash[] = self::getSetting($sid);
        }

        return array(
            "results" => $infohash,
            "num_results" => self::getNumSettings()
        );
    }


    /**
     * Deletes an extended client field. This also has various ramifications throughout the
     * rest of the script, so it tidies up them all. Namely:
     *  -- it deletes any data added for clients in the removed field
     *  -- it deletes any Client Map View filters that map to this field
     */
    public static function deleteSetting($sid, $L)
    {
        $db = Core::$db;

        $db->query("DELETE FROM {PREFIX}module_arbitrary_setting_options WHERE sid = :sid");
        $db->bind("sid", $sid);
        $db->execute();

        $db->query("DELETE FROM {PREFIX}module_arbitrary_settings WHERE sid = :sid");
        $db->bind("sid", $sid);
        $db->execute();

        self::reorderSettings();

        return array(true, $L["notify_setting_deleted"]);
    }


    /**
     * Returns all information about a field.
     */
    public static function getSetting($sid)
    {
        $db = Core::$db;

        $db->query("
            SELECT *
            FROM   {PREFIX}module_arbitrary_settings
            WHERE  sid = :sid
        ");
        $db->bind("sid", $sid);
        $db->execute();

        $info = $db->fetch();
        $info["options"] = array();

        if ($info["field_type"] == "select" || $info["field_type"] == "multi-select" ||
            $info["field_type"] == "radios" || $info["field_type"] == "checkboxes") {
            $db->query("
                SELECT *
                FROM   {PREFIX}module_arbitrary_setting_options
                WHERE  sid = :sid
                ORDER BY field_order ASC
            ");
            $db->bind("sid", $sid);
            $db->execute();

            $info["options"] = $db->fetchAll();
        }

        return $info;
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
    public static function updateSettingsOrder($info, $L)
    {
        $db = Core::$db;

        // loop through all the fields in $info that are being re-sorted and compile a list of
        // view_id => order pairs.
        $new_field_orders = array();
        foreach ($info as $key => $value) {
            if (preg_match("/^setting_(\d+)_order$/", $key, $match)) {
                $sid = $match[1];
                $new_field_orders[$sid] = $value;
            }
        }

        // okay! Since we may have only updated a *subset* of all fields (the fields page is
        // arranged in pages), get a list of ALL extended client fields, add them to
        // $new_field_orders and sort the entire lot of them in one go
        $db->query("
            SELECT sid, setting_order
            FROM   {PREFIX}module_arbitrary_settings
        ");
        $db->execute();

        foreach ($db->fetchAll() as $row) {
            if (!array_key_exists($row["sid"], $new_field_orders)) {
                $new_field_orders[$row["sid"]] = $row["setting_order"];
            }
        }

        // sort by the ORDER (the value - non-key - of the hash)
        asort($new_field_orders);

        $count = 1;
        foreach ($new_field_orders as $sid => $order) {
            $db->query("
                UPDATE {PREFIX}module_arbitrary_settings
                SET	   setting_order = :setting_order
                WHERE  sid = :sid
            ");
            $db->bindAll(array(
                "setting_order" => $count,
                "sid" => $sid
            ));
            $db->execute();

            $count++;
        }

        // return success
        return array(true, $L["notify_settings_order_updated"]);
    }


    /**
     * Called whenever a user deletes a field. This updates the field order.
     */
    public static function reorderSettings()
    {
        $db = Core::$db;

        $db->query("SELECT sid FROM {PREFIX}module_arbitrary_settings ORDER BY setting_order ASC");
        $db->execute();
        $results = $db->fetchAll();

        $order = 1;
        foreach ($results as $row) {
            $sid = $row["sid"];
            $db->query("
                UPDATE {PREFIX}module_arbitrary_settings
                SET    setting_order = :setting_order
                WHERE  sid = :sid
            ");
            $db->bindAll(array(
                "setting_order" => $order,
                "sid" => $sid
            ));
            $db->execute();
            $order++;
        }
    }


    public static function getNumSettings ()
    {
        $db = Core::$db;

        $db->query("SELECT count(*) FROM {PREFIX}module_arbitrary_settings");
        $db->execute();

        return $db->fetch(PDO::FETCH_COLUMN);
    }


    public static function getNextSettingId()
    {
        $db = Core::$db;

        $db->query("SELECT setting_order FROM {PREFIX}module_arbitrary_settings ORDER BY setting_order DESC LIMIT 1");
        $db->execute();

        $last_order = $db->fetch(PDO::FETCH_COLUMN);

        $next_order = 1;
        if (!empty($last_order)) {
            $next_order = $last_order + 1;
        }

        return $next_order;
    }
}

