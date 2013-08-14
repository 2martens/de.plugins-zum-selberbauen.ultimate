{hascontent}
	<ul class="sidebarBoxList">
		{content}
			{foreach from=$contents key=contentID item=content}
				{if $content->publishDateObject !== null}
					{assign var=date value=$content->publishDateObject->format('Y-m-d')}
				{else}
					{assign var=date value=''}
				{/if}
				{if $content->status == 3}
					<li class="box24">
						<a href="{linkExtended application='ultimate' date=$date contentSlug=$content->contentSlug}{/linkExtended}" class="framed">{@$content->authorProfile->getAvatar()->getImageTag(24)}</a>
						
						<div class="sidebarBoxHeadline">
							<h3><a href="{linkExtended application='ultimate' date=$date contentSlug=$content->contentSlug}{/linkExtended}" {if $content->isVisible} class="ultimateContentLink"{/if} data-content-id="{@$content->contentID}" data-sort-order="DESC" title="{$content->getLangTitle()}">{$content->getLangTitle()}</a></h3>
							<small>{if $content->authorID}<a href="{link controller='User' object=$content->author}{/link}" class="userLink" data-user-id="{@$content->authorID}">{$content->author->username}</a>{else}{$content->author->username}{/if} - {@$content->publishDate|time}</small>
						</div>
					</li>
				{/if}
			{/foreach}
		{/content}
	</ul>
{/hascontent}