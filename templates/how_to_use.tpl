{include file='modules_header.tpl'}

  <table cellpadding="0" cellspacing="0">
  <tr>
    <td width="45"><a href="index.php"><img src="images/icon_arbitrary_settings.png" border="0" width="34" height="34" /></a></td>
    <td class="title">
      <a href="../../admin/modules">{$LANG.word_modules}</a>
      <span class="joiner">&raquo;</span>
      <a href="./">{$L.module_name}</a>
      <span class="joiner">&raquo;</span>
      {$L.phrase_how_to_use}
    </td>
  </tr>
  </table>

  {include file='messages.tpl'}

  <div class="margin_bottom_large">
    {$L.text_how_to_use1}
  </div>

  <div style="border: 1px solid #666666; padding: 3px" class="margin_bottom_large">
    <textarea name="code_field" id="code_field" style="width:100%; height:80px"><?php
require_once("{$g_root_dir}/global/library.php");
$settings = ft_get_module_settings("", "arbitrary_settings");
?></textarea>
  </div>

  <script>
    var html_editor = new CodeMirror.fromTextArea("code_field", {literal}{{/literal}
    parserfile: ["parsexml.js", "parsecss.js", "tokenizejavascript.js", "parsejavascript.js",
                 "../contrib/php/js/tokenizephp.js", "../contrib/php/js/parsephp.js", "../contrib/php/js/parsephphtmlmixed.js"],
    stylesheet: ["{$g_root_url}/global/codemirror/css/xmlcolors.css", "{$g_root_url}/global/codemirror/css/jscolors.css",
                 "{$g_root_url}/global/codemirror/css/csscolors.css", "{$g_root_url}/global/codemirror/contrib/php/css/phpcolors.css"],
    path:       "{$g_root_url}/global/codemirror/js/"
    {literal}});{/literal}
  </script>

  <div class="margin_bottom_large">
    {$L.text_how_to_use2}
  </div>

{include file='modules_footer.tpl'}
