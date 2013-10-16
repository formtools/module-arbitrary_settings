{include file='modules_header.tpl'}

  <table cellpadding="0" cellspacing="0">
  <tr>
    <td width="45"><a href="index.php"><img src="images/icon_arbitrary_settings.png" border="0" width="34" height="34" /></a></td>
    <td class="title">
      <a href="../../admin/modules">{$LANG.word_modules}</a>
      <span class="joiner">&raquo;</span>
      <a href="./">{$L.module_name}</a>
      <span class="joiner">&raquo;</span>
      {$L.phrase_edit_setting}
    </td>
  </tr>
  </table>

  {include file='messages.tpl'}

  <form action="{$same_page}" method="post" onsubmit="return rsv.validate(this, rules)">
    <input type="hidden" name="num_rows" id="num_rows" value="{$setting_info.options|@count}" />
    <input type="hidden" name="field_order" id="field_order" value="{$setting_info.field_order}" />
    <input type="hidden" name="placeholder" value="" />

    <table cellspacing="1" cellpadding="1" border="0" class="margin_bottom_large">
    <tr>
      <td width="150">{$L.phrase_setting_label}</td>
      <td><input type="text" name="setting_label" style="width:589px" value="{$setting_info.setting_label|escape}" /></td>
    </tr>
    <tr>
      <td valign="top">{$L.phrase_setting_identifier}</td>
      <td>
        <input type="text" name="setting_identifier" style="width:200px" maxlength="100" value="{$setting_info.setting_identifier|escape}" />
        <div class="hint">{$L.text_setting_identifier_hint}</div>
      </td>
    </tr>
    <tr>
      <td>{$L.phrase_field_type}</td>
      <td>
        <select name="field_type" id="field_type" onchange="as_ns.change_field_type(this.value)">
          <option value="" {if $setting_info.field_type == ""}selected{/if}>{$LANG.phrase_please_select}</option>
          <option value="textbox" {if $setting_info.field_type == "textbox"}selected{/if}>{$LANG.word_textbox}</option>
          <option value="textarea" {if $setting_info.field_type == "textarea"}selected{/if}>{$LANG.word_textarea}</option>
          <option value="radios" {if $setting_info.field_type == "radios"}selected{/if}>{$LANG.phrase_radio_buttons}</option>
          <option value="checkboxes" {if $setting_info.field_type == "checkboxes"}selected{/if}>{$LANG.word_checkboxes}</option>
          <option value="select" {if $setting_info.field_type == "select"}selected{/if}>{$LANG.word_dropdown}</option>
          <option value="multi-select" {if $setting_info.field_type == "multi-select"}selected{/if}>{$LANG.phrase_multi_select}</option>
        </select>
      </td>
    </tr>
    </table>

    <div id="field_options_div" {if $setting_info.field_type == "textbox" || $setting_info.field_type == "textarea" || $setting_info.field_type == "password"}style="display:none"{/if}>
      <div class="margin_bottom_large subtitle underline">{$LANG.phrase_field_options|upper}</div>
      <table>
        <tr>
          <td width="140">{$L.word_orientation}</td>
          <td>
            <input type="radio" name="field_orientation" id="fo1" value="horizontal"
              {if $setting_info.field_orientation == "horizontal"}checked{/if}
              {if $setting_info.field_type == "select" || $setting_info.field_type == "multi-select"}disabled{/if} />
              <label for="fo1">{$LANG.word_horizontal}</label>
            <input type="radio" name="field_orientation" id="fo2" value="vertical"
              {if $setting_info.field_orientation == "vertical"}checked{/if}
              {if $setting_info.field_type == "select" || $setting_info.field_type == "multi-select"}disabled{/if} />
              <label for="fo2">{$LANG.word_vertical}</label>
            <input type="radio" name="field_orientation" id="fo3" value="na"
              {if $setting_info.field_orientation == "na"}checked{/if}
              {if $setting_info.field_type == "radios" || $setting_info.field_type == "checkboxes"}disabled{/if} />
              <label for="fo3">{$LANG.word_na}</label>
          </td>
        </tr>
        <tr>
          <td width="140" valign="top">{$L.phrase_field_option_source}</td>
          <td>
                <table cellspacing="1" cellpadding="0" id="field_options_table" class="list_table" style="width: 448px">
                <tbody>
                  <tr>
                    <th width="40"> </th>
                    <th>{$LANG.phrase_display_text}</th>
                    <th class="del"></th>
                  </tr>
                  {foreach from=$setting_info.options item=option name=row}
                    {assign var=count value=$smarty.foreach.row.iteration}
                      <tr id="row_{$count}">
                        <td class="medium_grey" align="center" id="field_option_{$count}_order">{$count}</td>
                        <td><input type="text" style="width:99%" name="field_option_text_{$count}" value="{$option.option_text|escape}" /></td>
                        <td class="del"><a href="#" onclick="as_ns.delete_field_option({$count})"></a></td>
                      </tr>
                    {/foreach}
                  </tbody>
                </table>

                <div>
                  <a href="#" onclick="as_ns.add_field_option(null, null)">{$LANG.phrase_add_row}</a>
                </div>

          </td>
        </tr>
      </table>
    </div>

    <p>
      <input type="submit" name="update" value="{$LANG.word_update}" />
      <input type="button" name="delete" value="{$LANG.word_delete}" class="red" onclick="page_ns.delete_field({$setting_info.sid})" />
    </p>

  </form>

{include file='modules_footer.tpl'}
