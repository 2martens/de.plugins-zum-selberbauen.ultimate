{if $__searchAreaInitialized|empty}
    {capture assign='__searchInputPlaceholder'}{lang}ultimate.content.searchContents{/lang}{/capture}
    {capture assign='__searchHiddenInputFields'}<input type="hidden" name="types[]" value="de.plugins-zum-selberbauen.ultimate.content" />{/capture}
    {capture assign='__searchAreaInitialized'}<input type="hidden" name="blabla" value="1" />{/capture}
{/if}
