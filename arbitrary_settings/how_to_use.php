<?php

require_once("../../global/library.php");
ft_init_module_page();

$folder = dirname(__FILE__);
require_once("$folder/library.php");

// ------------------------------------------------------------------------------------------------

$page_vars = array();
$page_vars["head_title"] = $L["module_name"];
$page_vars["head_string"] =<<< END
<script src="$g_root_url/global/codemirror/js/codemirror.js"></script>
END;

ft_display_module_page("templates/how_to_use.tpl", $page_vars);
