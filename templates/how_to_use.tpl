{ft_include file='modules_header.tpl'}

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

    {ft_include file='messages.tpl'}

    <div class="margin_bottom_large">
        {$L.text_how_to_use1}
    </div>

    <div style="border: 1px solid #666666; padding: 3px; height: 90px; overflow: hidden" class="margin_bottom_large">
        <textarea name="code_field" id="code_field" style="width:100%; height: 90px"><?php
require_once("{$g_root_dir}/global/library.php");
$settings = Modules::getModuleSettings("", "arbitrary_settings");
?></textarea>
    </div>

    <script>
    var html_editor = new CodeMirror.fromTextArea(document.getElementById("code_field"), {literal}{{/literal}
        mode: "php"
    {literal}});{/literal}
    </script>

  <div class="margin_bottom_large">
    {$L.text_how_to_use2}
  </div>

{ft_include file='modules_footer.tpl'}
