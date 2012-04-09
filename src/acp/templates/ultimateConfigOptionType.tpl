<select id="{$option->optionName}" name="values[{$option->optionName}]" size="1">
{foreach from=$selectOptions key=configID item=configTitle}
    <option value="{$configID}"{if $value == $configID} selected="selected"{/if}>{lang}{@$configTitle}{/lang}</option>
{/foreach}
</select>