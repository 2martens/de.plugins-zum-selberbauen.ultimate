{include file='documentHeader' sandbox=false}
<head>
    <title>{@$configTitle}</title>
    <meta name="description" content="{@$metaDescription}" />
    <meta name="keywords" content="{@$metaKeywords}" />
    {include file='headInclude' sandbox=false}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

{if $leftColumn}
<div class="ultimateLeft">
	{foreach from=$entriesLeft item=$entry}
	<div id="left-{$entry->getComponentID()}-{$entry->getContentID()}">{@$entry->getOutput()}</div>
	{/foreach}
</div>
{/if}
{if $rightColumn}
<div class="ultimateRight">
	{foreach from=$entriesRight item=$entry}
	<div id="right-{$entry->getComponentID()}-{$entry->getContentID()}">{@$entry->getOutput()}</div>
	{/foreach}
</div>
{/if}
{if $centerColumn}
<div class="ultimateCenter{if $leftColumn} leftMargin{/if}{if $rightColumn} rightMargin{/if}">
	{foreach from=$entriesCenter item=$entry}
	<div id="center-{$entry->getComponentID()}-{$entry->getContentID()}">{@$entry->getOutput()}</div>
	{/foreach}
</div>
{/if}

{include file='footer' sandbox=false}
</body>
</html>