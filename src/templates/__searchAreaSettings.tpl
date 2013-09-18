{if $__searchAreaInitialized|empty}
	{if $__ultimate->getCurrentApplication() == 'wcf'}
		{capture assign='__searchInputPlaceholder'}{lang}wcf.global.search.enterSearchTerm{/lang}{/capture}
		{capture assign='__searchHiddenInputFields'}<input type="hidden" name="types[]" value="de.plugins-zum-selberbauen.ultimate.content" />{*
		*}<input type="hidden" name="types[]" value="com.woltlab.wbb.post" />{/capture}
		{capture assign='__searchAreaInitialized'}<input type="hidden" name="blabla" value="1" />{/capture}
	{/if}
	{if $__ultimate->getCurrentApplication() == 'ultimate'}
		{capture assign='__searchInputPlaceholder'}{lang}ultimate.content.searchContents{/lang}{/capture}
		{capture assign='__searchHiddenInputFields'}<input type="hidden" name="types[]" value="de.plugins-zum-selberbauen.ultimate.content" />{/capture}
		{capture assign='__searchAreaInitialized'}<input type="hidden" name="blabla" value="1" />{/capture}
	{/if}
{/if}