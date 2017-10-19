/**
 * This code is used for managing the Field Options for checkboxes, radio buttons and dropdowns. It's
 * just a copy of the Extended Client Field module's slimmed down version of manage_field_option_groups.js
 * from the Core. Refactored.
 */

as_ns = {};
as_ns.num_rows = null; // set by onload function
as_ns.tmp_deleted_field_option_rows = [];
as_ns.current_field_type = null;


/**
 * This deletes a field option. Note: it doesn't remove the option in memory; that is handled by
 * _update_current_field_settings(), which is called when the user leaves the page & when
 */
as_ns.delete_field_option = function(row) {
  var order = parseInt($("#field_option_" + row + "_order").html());

  $("#row_" + row).remove();
  as_ns.tmp_deleted_field_option_rows.push(row);

  // update the order of all subsequent rows
  for (var i=row+1; i<=as_ns.num_rows; i++) {
    if (!$("#field_option_" + i + "_order").length) {
      continue;
    }
    $("#field_option_" + i + "_order").html(order);
    order++;
  }

  return false;
}


as_ns.delete_all_rows = function() {
  for (var i=1; i<=as_ns.num_rows; i++) {
    if (!$("#field_option_" + i + "_order").length) {
      continue;
    }
    $("#row_" + i).remove();
  }
  as_ns.num_rows = 0;
}


/**
 * Adds a field option for the currently selected field (dropdown, radio or checkbox).
 */
as_ns.add_field_option = function(default_val, default_txt) {
  var next_id = ++as_ns.num_rows;

  var row = document.createElement("tr");
  row.setAttribute("id", "row_" + next_id);

  // [1] first cell: row number
  var td1 = document.createElement("td");
  td1.setAttribute("align", "center");
  $(td1).addClass("medium_grey");
  td1.setAttribute("id", "field_option_" + next_id + "_order");
  var num_deleted_rows = as_ns.tmp_deleted_field_option_rows.length;
  var row_num_label = next_id - num_deleted_rows;
  td1.appendChild(document.createTextNode(row_num_label));

  // [2] second <td> cell: "display text" field
  var td2 = document.createElement("td");
  var title = document.createElement("input");
  title.setAttribute("type", "text");
  title.setAttribute("name", "field_option_text_" + next_id);
  title.setAttribute("id", "field_option_text_" + next_id);
  title.style.cssText = "width: 98%";
  if (default_txt != null) {
    title.setAttribute("value", default_txt);
  }
  td2.appendChild(title);

  // [4] delete column
  var td3 = document.createElement("td");
  td3.setAttribute("align", "center");
  td3.className = "del";
  var del_link = document.createElement("a");
  del_link.setAttribute("href", "#");
  $(del_link).bind("click", { next_id: next_id }, function(e) {
    as_ns.delete_field_option(e.data.next_id);
  });
  td3.appendChild(del_link);

  // add the table data cells to the row
  row.appendChild(td1);
  row.appendChild(td2);
  row.appendChild(td3);

  // add the row to the table
  var tbody = $("#field_options_table tbody")[0];
  tbody.appendChild(row);

  $("#num_rows").val(as_ns.num_rows);

  return false;
}


/**
 * This relies on the ecf.current_field_type having been set in the page.
 */
as_ns.change_field_type = function(choice) {
  if (choice == as_ns.current_field_type) {
    return;
  }
  if (choice == "radios" || choice == "checkboxes" || choice == "select" || choice == "multi-select") {
    if ($("#field_options_div")[0].style.display == "none") {
      $("#field_options_div").show("blind");
    }
    if (choice == "radios" || choice == "checkboxes") {
      $("#fo1, #fo2").attr("disabled", "");
      $("#fo3").attr("disabled", "disabled");
      if ($("#fo3").attr("checked")) {
        $("#fo1").attr("checked", "checked");
      }
    } else {
      $("#fo1, #fo2").attr("disabled", "disabled");
      $("#fo3").attr("disabled", "");
      $("#fo3").attr("checked", "checked");
    }
  } else {
    if ($("#field_options_div")[0].style.display != "none") {
      $("#field_options_div").hide("blind");
    }
  }

  as_ns.current_field_type = choice;
}
