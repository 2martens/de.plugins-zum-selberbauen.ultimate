{if $leftColumn}
<div class="ultimateLeft">
    {foreach from=$entriesLeft item=$entry}
    <div id="left-{$entry->getComponentID()}-{$entry->getContentID()}">{@$entry->getContent()}</div>
    {/foreach}
</div>
{/if}
{if $rightColumn}
<div class="ultimateRight">
    {foreach from=$entriesRight item=$entry}
    <div id="right-{$entry->getComponentID()}-{$entry->getContentID()}">{@$entry->getContent()}</div>
    {/foreach}
</div>
{/if}
{if $centerColumn}
<div class="ultimateCenter{if $leftColumn} leftMargin{/if}{if $rightColumn} rightMargin{/if}">
    <fieldset class="ultimateInvisible">
    {foreach from=$entriesCenter item=$entry}
    <div id="center-{$entry->getComponentID()}-{$entry->getContentID()}">{@$entry->getContent()}</div>
    {/foreach}
    </fieldset>
</div>
{/if}