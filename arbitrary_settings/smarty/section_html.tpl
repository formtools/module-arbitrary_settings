</table>

{if $title}
  <p class="subtitle underline margin_bottom_large">{$title}</p>
{else}
  <div class="margin_bottom_large"> </div>
{/if}

<table class="list_table margin_bottom_large" cellpadding="0" cellspacing="1">
{foreach from=$settings item=setting name=row}
  {assign var=sid value=$setting.sid}
  <tr>
    <td class="pad_left_small" width="200">{$setting.setting_label}</td>
    <td>
      {if $setting.field_type == "textbox"}
        <input type="text" name="arbitrary_setting_{$sid}" value="{$setting.content|escape}" size="50" />
      {elseif $setting.field_type == "textarea"}
        <textarea name="arbitrary_setting_{$sid}" style="width:98%; height: 60px">{$setting.content}</textarea>
      {elseif $setting.field_type == "radios"}

        {foreach from=$setting.options key=k2 item=option name=row}
          {assign var="count" value=$smarty.foreach.row.iteration}
          {assign var="escaped_value" value=$option.option_text}
          <input type="radio" name="arbitrary_setting_{$sid}" id="sid_{$sid}_{$count}" value="{$option.option_text|escape}"
            {if $escaped_value == $setting.content}checked="checked"{/if} />
            <label for="sid_{$sid}_{$count}">{$option.option_text|escape}</label>
            {if $setting.field_orientation == "vertical"}<br />{/if}
        {/foreach}

      {elseif $setting.field_type == "checkboxes"}

        {foreach from=$setting.options key=k2 item=option name="row"}
          {assign var="count" value=$smarty.foreach.row.iteration}
          {assign var="escaped_value" value=$option.option_text|escape}
          <input type="checkbox" name="arbitrary_setting_{$sid}[]" id="sid_{$sid}_{$count}" value="{$option.option_text|escape}"
            {if $escaped_value|in_array:$setting.content}checked="checked"{/if} />
            <label for="sid_{$sid}_{$count}">{$option.option_text|escape}</label>
            {if $setting.field_orientation == "vertical"}<br />{/if}
        {/foreach}

      {elseif $setting.field_type == "select"}

        <select name="arbitrary_setting_{$sid}">
          {foreach from=$setting.options key=k2 item=option}
            {assign var="escaped_value" value=$option.option_text|escape}
            <option value="{$option.option_text|escape}" {if $escaped_value == $setting.content}selected="selected"{/if}>{$option.option_text}</option>
           {/foreach}
        </select>

      {elseif $setting.field_type == "multi-select"}

        <select name="arbitrary_setting_{$sid}[]" multiple size="4">
          {foreach from=$setting.options key=k2 item=option}
            {assign var="escaped_value" value=$option.option_text|escape}
            <option value="{$option.option_text|escape}"
              {if $escaped_value|in_array:$setting.content}selected="selected"{/if}>{$option.option_text}</option>
           {/foreach}
        </select>

      {/if}
    </td>
  </tr>
{/foreach}

