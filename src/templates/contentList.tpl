{* default values *}
{if !$showLikeColumn|isset}{assign var='showLikeColumn' value=true}{/if}
{if !$showViewColumn|isset}{assign var='showViewColumn' value=true}{/if}

{foreach from=$objects item=content}
{if $content->publishDate != 0}
	<tr id="content{@$content->contentID}" class="ultimateContent jsClipboardObject" data-content-id="{@$content->contentID}" data-element-id="{@$content->contentID}">
		<td class="columnText columnSubject">
			<h3>
			{if $content->page|isset}
				<a href="{link application='ultimate' pageslug=$content->page->pageSlug}{/link}" class="messageGroupLink" data-content-id="{@$content->contentID}">{$content->page->getTitle()|wordwrap:35}</a>
			{else}
				<a href="{link application='ultimate' date=$content->publishDateObject->format('Y-m-d') contentslug=$content->contentSlug}{/link}" class="messageGroupLink" data-content-id="{@$content->contentID}">{$content->getTitle()|wordwrap:35}</a>
			{/if}
			</h3>
			
			<small>
				{if $content->authorID}<a href="{link controller='User' object=$content->author}{/link}" class="userLink" data-user-id="{@$content->authorID}">{$content->author->username}</a>{else}{$content->author->username}{/if}
				- {@$content->publishDate|time}
			</small>
			
			{event name='contentData'}
		</td>
		{if $showLikeColumn && MODULE_LIKE && $__wcf->getSession()->getPermission('user.like.canViewLike')}<td class="columnStatus columnLikes">{if $content->likes || $content->dislikes}<span class="likesBadge badge jsTooltip {if $content->cumulativeLikes > 0}green{elseif $content->cumulativeLikes < 0}red{/if}" title="{lang likes=$content->likes dislikes=$content->dislikes}wcf.like.tooltip{/lang}">{if $content->cumulativeLikes > 0}+{elseif $content->cumulativeLikes == 0}&plusmn;{/if}{#$content->cumulativeLikes}</span>{/if}</td>{/if}
		{if $showViewColumn}<td class="columnDigits columnViews">{#$content->views}</td>{/if}
		
		{event name='columns'}
	</tr>
{/if}
{/foreach}
