{include file="documentHeader"}
<head>
	<title>{if $title|isset}{lang}{@$title}{/lang}{if $requestType == 'category'}{if $pageNo > 1} - {lang}wcf.page.pageNo{/lang}{/if}{/if} - {/if}{lang}{PAGE_TITLE}{/lang}</title>
	
	{include file='headInclude'}
	
	{if $requestType == 'category'}
		{if $__wcf->getUser()->userID}
			<link rel="alternate" type="application/rss+xml" title="{lang}wcf.global.button.rss{/lang}" href="{link application='ultimate' controller='CategoryFeed' id=$requestObject->categoryID appendSession=false}at={@$__wcf->getUser()->userID}-{@$__wcf->getUser()->accessToken}{/link}" />
		{else}
			<link rel="alternate" type="application/rss+xml" title="{lang}wcf.global.button.rss{/lang}" href="{link application='ultimate' controller='CategoryFeed' id=$requestObject->categoryID appendSession=false}{/link}" />
		{/if}
		
		{if $pageNo < $pages}
			<link rel="next" href="{linkExtended application='ultimate' category='category' categorySlug=$requestObject->categorySlug}pageNo={@$pageNo+1}{/linkExtended}" />
		{/if}
		{if $pageNo > 1}
			<link rel="prev" href="{linkExtended application='ultimate' category='category' categorySlug=$requestObject->categorySlug}{if $pageNo > 2}pageNo={@$pageNo-1}{/if}{/linkExtended}" />
		{/if}
		<link rel="canonical" href="{linkExtended application='ultimate' category='category' categorySlug=$requestObject->categorySlug}{if $pageNo > 1}pageNo={@$pageNo}{/if}{/linkExtended}" />
	{/if}
	{if $requestType == 'index'}
		{if $__wcf->getUser()->userID}
			<link rel="alternate" type="application/rss+xml" title="{lang}wcf.global.button.rss{/lang}" href="{link application='ultimate' controller='CategoryFeed' appendSession=false}at={@$__wcf->getUser()->userID}-{@$__wcf->getUser()->accessToken}{/link}" />
		{else}
			<link rel="alternate" type="application/rss+xml" title="{lang}wcf.global.button.rss{/lang}" href="{link application='ultimate' controller='CategoryFeed' appendSession=false}{/link}" />
		{/if}
	{/if}
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

{include file='userNotice'}

			<!-- custom area -->
			{@$customArea}
			<!-- /custom area -->
			
{include file='footer' application='ultimate'}

</body>
</html>
