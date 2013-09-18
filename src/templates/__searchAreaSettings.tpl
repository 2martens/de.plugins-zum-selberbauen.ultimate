{if $__ultimate->isActiveApplication() && $__searchAreaInitialized|empty}
	{capture assign='__searchInputPlaceholder'}{lang}wcf.global.search.enterSearchTerm{/lang}{/capture}
	{capture append='__searchDropdownOptions'}<label><input type="checkbox" name="subjectOnly" value="1" /> {lang}wcf.search.subjectOnly{/lang}</label>{/capture}
	{capture assign='__searchHiddenInputFields'}<input type="hidden" name="types[]" value="de.plugins-zum-selberbauen.ultimate.content" />{*
	*}<input type="hidden" name="types[]" value="com.woltlab.wbb.post" />{/capture}
	{capture assign='__searchAreaInitialized'}<!-- bla bla bla -->{/capture}
{/if}