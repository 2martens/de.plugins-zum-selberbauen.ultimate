<select id="{$option->optionName}" name="values[{$option->optionName}]">
	<option value="0"></option>
	{foreach from=$linkCategories key=categoryID item=category}
		<option value="{@$categoryID}"{if $categoryID == $value} selected="selected"{/if}>{$category->title|language}</option>
	{/foreach}
</select>