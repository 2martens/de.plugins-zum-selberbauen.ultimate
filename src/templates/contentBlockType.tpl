<script data-relocate="true" type="text/javascript">
//<![CDATA[
$(function() {	
	{if MODULE_LIKE && $__wcf->getSession()->getPermission('user.like.canViewLike')}new ULTIMATE.Content.Like({if $__wcf->getUser()->userID && $__wcf->getSession()->getPermission('user.like.canLike')}1{else}0{/if}, {@LIKE_ENABLE_DISLIKE}, {@LIKE_SHOW_SUMMARY}, {@LIKE_ALLOW_FOR_OWN_CONTENT});{/if}
	{if $__wcf->user->userID}
		{if $__wcf->getSession()->getPermission('admin.content.ultimate.canEditContent')}
			var $inlineEditor = new ULTIMATE.Content.InlineEditor(0);
		{/if}
	{/if}
});
//]]>
</script>

{if !$anchor|isset}{assign var=anchor value=$__wcf->getAnchor('top')}{/if}
<div id="block-{$blockID}" class="block block-type-content" data-height="{$height}">
	
	{if $requestType == 'category'}
		<div class="contentNavigation">
			{pagesExtended print=true pages=$amountOfPages assign=pagesLinks application='ultimate' link="pageNo=%d" category='category' categorySlug=$requestObject->categorySlug}
		</div>
	{/if}
	
	{if $requestType == 'category' || $requestType == 'index'}
		<div class="marginTop">
			<ul class="messageList" data-type="de.plugins-zum-selberbauen.ultimate.content">
	{/if}
	{assign var=displayedFeaturedContents value=0}
	{foreach from=$contents key=contentID item=content}
		{if $content->status == 3}
			{if $requestType == 'category' || $requestType == 'index'}
				<li id="content{@$content->contentID}" class="marginTop">
			{/if}
			
				<article itemtype="http://schema.org/Article" itemscope="" class="{if $requestType == 'category' || $requestType == 'index'}ultimateContent message dividers jsClipboardObject jsMessage{else}container containerPadding marginTop{/if}" 
				data-can-edit="{if $__wcf->getSession()->getPermission('admin.content.ultimate.canEditContent')}1{else}0{/if}"
				data-is-i18n="{literal}<?php if (strpos($this->v['content']->contentText, 'ultimate.content.') !== false) { ?>{/literal}1{literal}<?php } else { ?>{/literal}0{literal}<?php } ?>{/literal}"
				data-object-id="{@$contentID}"
				data-object-type="de.plugins-zum-selberbauen.ultimate.likeableContent"
				data-like-liked="{if $likeData[$contentID]|isset}{@$likeData[$contentID]->liked}{/if}" 
				data-like-likes="{if $likeData[$contentID]|isset}{@$likeData[$contentID]->likes}{else}0{/if}" 
				data-like-dislikes="{if $likeData[$contentID]|isset}{@$likeData[$contentID]->dislikes}{else}0{/if}" 
				data-like-users='{if $likeData[$contentID]|isset}{ {implode from=$likeData[$contentID]->getUsers() item=likeUser}"{@$likeUser->userID}": { "username": "{$likeUser->username|encodeJSON}" }{/implode} }{else}{ }{/if}' 
				data-user-id="{@$content->authorID}">
					
					{if !$block->hideContent}
						{if $requestType == 'category' || $requestType == 'index'}
						<div>
							<section class="messageContent">
							<div>
						{/if}
					
						<header class="{if $requestType == 'category' || $requestType == 'index'}messageHeader{else}boxHeadline{/if}">
							{if $requestType == 'category' || $requestType == 'index'}
								<div class="messageHeadline">
							{/if}
							
								{if !$block->hideTitles}
									<h1 itemprop="name">
										{if $requestType != 'content' && $requestType != 'page'}
											<a class="link" href="{linkExtended application='ultimate' date=$content->publishDateObject->format('Y-m-d') contentSlug=$content->contentSlug}{/linkExtended}" itemprop="url">{$content->getLangTitle()}</a>
										{else}
											{$content->getLangTitle()}
										{/if}
									</h1>
								{/if}
								
								{if $requestType == 'category' || $requestType == 'index'}
									<p class="likeContainer">
								{/if}
								{if $contentMetaDisplaySelected[$requestType]|isset || $contentMetaDisplaySelected|count == 0}
									{hascontent}
										{if $requestType == 'category' || $requestType == 'index'}
											<span class="meta" id="metaAbove-{$contentID}">
										{else}
											<small class="meta" id="metaAbove-{$contentID}">
										{/if}
											
											{content}
												{if $metaAbove[$contentID]|isset && $metaAbove[$contentID] != ""}
													{@$metaAbove[$contentID]}
												{/if}
												{if $metaAbove_i18n[$contentID][$__wcf->getLanguage()->__get('languageID')]|isset}
													{@$metaAbove_i18n[$contentID][$__wcf->getLanguage()->__get('languageID')]}
												{/if}
											{/content}
										
										{if $requestType == 'category' || $requestType == 'index'}
											</span>
										{else}
											</small>
										{/if}
									{/hascontent}
								{/if}
								{if $requestType == 'category' || $requestType == 'index'}
									</p>
								{/if}
							
							{if $requestType == 'category' || $requestType == 'index'}
								</div>
							{/if}
							
							{if $requestType == 'content'}
								{hascontent}
									<p class="abstract" itemprop="description">{content}{lang}{$content->contentDescription}{/lang}{/content}</p>
								{/hascontent}
							{/if}
						</header>
						{if $block->contentBodyDisplay != 'hide'}
							{if $requestType == 'category' || $requestType == 'index'}
								<div class="messageBody">
									<div>
							{/if}
								
								<div itemprop="articleBody" id="content-{$contentID}" 
									class="content {implode from=$content->categories item=category glue=' '}category-{$category->categorySlug}{/implode} 
									{implode from=$content->tags[$__wcf->getLanguage()->__get('languageID')] item=tag glue=''}tag-{$tag->getTitle()}{/implode}
									{if $requestType == 'category' || $requestType == 'index'} messageText{/if}">
									
									{if ($block->contentBodyDisplay == 'default' && ($displayedFeaturedContents < $block->featuredContents || $requestType == 'content' || $requestType == 'page')) || $block->contentBodyDisplay == 'full'}
										{counter name=displayedFeaturedContents assign=displayedFeaturedContents print=false start=0}
										{assign var=displayedFeaturedContents value=($displayedFeaturedContents + 1)}
										{if $requestType == 'content' || $requestType == 'page'}
											{@$content->getFormattedMessage()}
										{/if}
										{if $requestType == 'index' || $requestType == 'category'}
											<p>{@$content->getFormattedMessage()|truncateMore:0}</p>
										{/if}
									{else}
										<p>{@$content->getFormattedMessage()|truncateMore:ULTIMATE_GENERAL_CONTENT_CONTINUEREADINGLENGTH}</p>
									{/if}
								</div>
								
								{if $requestType == 'category' || $requestType == 'index'}
									</div>
								{/if}
								
								{if $requestType == 'category' || $requestType == 'index'}
									<div class="messageSignature">
								{/if}
									
									{if $contentMetaDisplaySelected[$requestType]|isset || $contentMetaDisplaySelected|count == 0}
										{hascontent}
											{if $requestType == 'category' || $requestType == 'index'}
												<div class="meta" id="metaBelow-{$contentID}">
											{else}
												<aside class="meta" id="metaBelow-{$contentID}">
											{/if}
											{content}
												{if $metaBelow[$contentID]|isset && $metaBelow[$contentID] != ""}
													{@$metaBelow[$contentID]}
												{/if}
												{if $metaBelow_i18n[$contentID][$__wcf->getLanguage()->__get('languageID')]|isset}
													{@$metaBelow_i18n[$contentID][$__wcf->getLanguage()->__get('languageID')]}
												{/if}
											{/content}
											{if $requestType == 'category' || $requestType == 'index'}
												</div>
											{else}
												</aside>
											{/if}
										{/hascontent}
									{/if}
								
								{if $requestType == 'category' || $requestType == 'index'}	
									</div>
								{/if}
								
								{if $requestType == 'category' || $requestType == 'index'}
									<div class="messageFooter">
								{/if}
								
								{if $requestType == 'category' || $requestType == 'index'}	
									</div>
								{/if}
								
								{if $requestType == 'category' || $requestType == 'index'}	
									<footer class="messageOptions">
										<nav class="jsMobileNavigation buttonGroupNavigation">
											<ul class="smallButtons buttonGroup">{*
												*}<li><a href="{linkExtended application='ultimate' date=$content->publishDateObject->format('Y-m-d') contentSlug=$content->contentSlug}{/linkExtended}" class="button"><span class="icon icon16 icon-arrow-right"></span> <span>{lang}wcf.global.button.readMore{/lang}</span></a></li>{*
												*}{if $__wcf->getSession()->getPermission('admin.content.ultimate.canEditContent')}<li><a title="{lang}wcf.acp.ultimate.content.edit{/lang}" class="button jsMessageEditButton"><span class="icon icon16 icon-pencil"></span> <span>{lang}wcf.global.button.edit{/lang}</span></a></li>{/if}{*
												*}<li class="toTopLink"><a href="{@$anchor}" title="{lang}wcf.global.scrollUp{/lang}" class="button jsTooltip"><span class="icon icon16 icon-arrow-up"></span> <span class="invisible">{lang}wcf.global.scrollUp{/lang}</span></a></li>{*
											*}</ul>
										</nav>
									</footer>
								{/if}
							
							{if $requestType == 'category' || $requestType == 'index'}
								</div>
							{/if}
						{/if}
						
						
						{if $requestType == 'category' || $requestType == 'index'}
							</div>
							</section>
						</div>
						{/if}
					{/if}
					
					{if $block->commentsVisibility == 'show' || ($block->commentsVisibility == 'auto' && $requestType == 'content')}
					<section class="content comments">
						<header class="containerHeadline">
							<h3>{lang}ultimate.content.comments{/lang}</h3>
						</header>
						{assign var='commentContainerID' value='content-'|concat:$contentID|concat:'-comments'}
						{assign var='commentList' value=$commentLists[$contentID]}
						{if MODULE_LIKE}
							{assign var=likeData value=$commentList->getLikeData()}
						{/if}
						{include file='__commentJavaScript' commentContainerID=$commentContainerID}
						
						{if $commentCanAdd}
							<ul id="content-{$contentID}-comments" class="commentList containerList" data-can-add="true" data-object-id="{@$contentID}" data-object-type-id="{@$commentObjectTypeID}" data-comments="{@$commentList->countObjects()}" data-last-comment-time="{@$commentList->getMinCommentTime()}">
								{include file='commentList' application='ultimate'}
							</ul>
						{else}
							{hascontent}
								<ul id="content-{$contentID}-comments" class="commentList containerList" data-can-add="false" data-object-id="{@$contentID}" data-object-type-id="{@$commentObjectTypeID}" data-comments="{@$commentList->countObjects()}" data-last-comment-time="{@$commentList->getMinCommentTime()}">
									{content}
										{include file='commentList' application='ultimate'}
									{/content}
								</ul>
							{hascontentelse}
								<div class="containerPadding">
									{* TODO: own lang variable *}
									{lang}wcf.user.profile.content.wall.noEntries{/lang}
								</div>
							{/hascontent}
						{/if}
					</section>
					{/if}
				</article>
			{if $requestType == 'category' || $requestType == 'index'}
				</li>
			{/if}
		{/if}
	{/foreach}
	
	{if $requestType == 'category' || $requestType == 'index'}
			</ul>
		</div>
	{/if}
	
	{if $requestType == 'category'}
		<div class="contentNavigation">
			{@$pagesLinks}
		</div>
	{/if}
</div>