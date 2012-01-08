<div class="ultimateContent">
{if $leftColumn}
<div class="ultimateLeft ultimatePage">
    {foreach from=$entriesLeft item=$entry}
    <div class="ultimateEntry" id="left-{$entry->getComponentID()}-{$entry->getContentID()}">{@$entry->getContent()}</div>
    {/foreach}
</div>
{/if}
{if $rightColumn}
<div class="ultimateRight ultimatePage">
    {foreach from=$entriesRight item=$entry}
    <div class="ultimateEntry" id="right-{$entry->getComponentID()}-{$entry->getContentID()}">{@$entry->getContent()}</div>
    {/foreach}
</div>
{/if}
{if $centerColumn}
<div class="ultimateCenter{if $leftColumn} leftMargin{/if}{if $rightColumn} rightMargin{/if}">
    <fieldset class="ultimateInvisible">
    {foreach from=$entriesCenter item=$entry}
    <div class="ultimateEntry" id="center-{$entry->getComponentID()}-{$entry->getContentID()}">{@$entry->getContent()}</div>
    {/foreach}
    </fieldset>
</div>
{/if}
</div>