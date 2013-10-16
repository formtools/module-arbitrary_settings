{include file='modules_header.tpl'}

  <table cellpadding="0" cellspacing="0">
  <tr>
    <td width="45"><a href="index.php"><img src="images/icon_arbitrary_settings.png" border="0" width="34" height="34" /></a></td>
    <td class="title">
      <a href="../../admin/modules">{$LANG.word_modules}</a>
      <span class="joiner">&raquo;</span>
      <a href="./">{$L.module_name}</a>
      <span class="joiner">&raquo;</span>
      {$L.word_labels}
    </td>
  </tr>
  </table>

  {include file='messages.tpl'}

  <div class="margin_bottom_large">
    {$L.module_description}
  </div>

  <form action="labels.php" method="post">

    <table cellspacing="0" cellpadding="0" class="list_table">
    <tr>
      <td class="pad_left_small" width="150">{$L.phrase_settings_title}</td>
      <td>
        <input type="text" name="settings_title" value="{$settings.settings_title}" style="width: 99%" />
        <div class="medium_grey">
          {$L.text_settings_title_hint}
        </div>
      </td>
    </table>

    <p>
      <input type="submit" name="update" value="{$LANG.word_update}" />
    </p>
  </form>

{include file='modules_footer.tpl'}
