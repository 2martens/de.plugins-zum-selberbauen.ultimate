{include file="documentHeader"}
<head>
	<title>{if $title|isset}{lang}{@$title}{/lang} - {/if}{lang}{PAGE_TITLE}{/lang}</title>
	
	{include file='headInclude' application='ultimate'}
</head>

<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{if $__boxSidebar|isset && $__boxSidebar}
	{capture assign='sidebar'}
		{@$__boxSidebar}
	{/capture}
{/if}
{if $sidebarOrientation|isset}
	{include file='header' application='ultimate' sidebarOrientation=''|concat:$sidebarOrientation}
{else}
	{include file='header' application='ultimate' sidebarOrientation='right'}
{/if}
			<!-- custom area -->
			{@$customArea}
			<!-- /custom area -->
			
{include file='footer' application='ultimate'}

</body>
</html>
