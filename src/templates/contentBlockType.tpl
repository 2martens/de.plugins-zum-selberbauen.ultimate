<script data-relocate="true" type="text/javascript">
//<![CDATA[
$(function() {	
	{if MODULE_LIKE && $__wcf->getSession()->getPermission('user.like.canViewLike') && $requestType != 'page'}new ULTIMATE.Content.Like({if $__wcf->getUser()->userID && $__wcf->getSession()->getPermission('user.like.canLike')}1{else}0{/if}, {@LIKE_ENABLE_DISLIKE}, {@LIKE_SHOW_SUMMARY}, {@LIKE_ALLOW_FOR_OWN_CONTENT});{/if}
	{if $__wcf->user->userID}
		{if $__wcf->getSession()->getPermission('user.ultimate.content.canEditContent')}
			var $inlineEditor = new ULTIMATE.Content.InlineEditor(0);
		{/if}
	{/if}
});
//]]>
</script>

{if !$anchor|isset}{assign var=anchor value=$__wcf->getAnchor('top')}{/if}
<div id="block-{$blockID}" class="block block-type-content">
	
	{if $requestType == 'category'}
		<div class="contentNavigation">
			{pagesExtended print=true pages=$amountOfPages assign=pagesLinks application='ultimate' link="pageNo=%d" category='category' categoryslug=$requestObject->categorySlug}
		</div>
	{/if}
	
	<div class="marginTop">
		<ul class="messageList" data-type="de.plugins-zum-selberbauen.ultimate.content">
	
	{assign var=displayedFeaturedContents value=0}
	{foreach from=$contents key=contentID item=content}
		{assign var=objectID value=$contentID}
		{if $content->publishDateObject !== null}
			{assign var=date value=$content->publishDateObject->format('Y-m-d')}
		{else}
			{assign var=date value=''}
		{/if}
		{*if $content->status == 3*}
			<li id="content{@$content->contentID}" class="marginTop">
			
				<article itemtype="http://schema.org/Article" itemscope="itemscope" class="ultimateContent{if $requestType == 'page'} ultimateWhiteBackgroundColor{/if} dividers">
					
					{if !$block->hideContent}
						<div class="message jsMessage messageReduced{if $requestType == 'page'} ultimateWhiteBackgroundColor{/if}"
				data-can-edit="{if $__wcf->getSession()->getPermission('user.ultimate.content.canEditContent')}1{else}0{/if}"
				data-is-i18n="{literal}<?php if (mb_strpos($this->v['content']->contentText, 'ultimate.content.') !== false) { ?>{/literal}1{literal}<?php } else { ?>{/literal}0{literal}<?php } ?>{/literal}"
				data-object-id="{@$contentID}"
				data-object-type="de.plugins-zum-selberbauen.ultimate.likeableContent" {*
				*}{if $requestType != 'page'}{*
				*}data-like-liked="{if $likeData[$contentID]|isset}{@$likeData[$contentID]->liked}{/if}" 
				data-like-likes="{if $likeData[$contentID]|isset}{@$likeData[$contentID]->likes}{else}0{/if}" 
				data-like-dislikes="{if $likeData[$contentID]|isset}{@$likeData[$contentID]->dislikes}{else}0{/if}" 
				data-like-users='{if $likeData[$contentID]|isset}{ {implode from=$likeData[$contentID]->getUsers() item=likeUser}"{@$likeUser->userID}": { "username": "{$likeUser->username|encodeJSON}" }{/implode} }{else}{ }{/if}' {*
				*}{/if}{*
				*}data-user-id="{@$content->authorID}">
							
							<section class="messageContent{if $requestType == 'page'} ultimateWhiteBackgroundColor{/if}">
								<div>
									<header class="messageHeader">
										<div class="messageHeadline">
										
											{if !$block->hideTitles}
												<h1 itemprop="name">
													{if $requestType != 'content' && $requestType != 'page' && ($queryModeSelected == 'default' || $fetchPageContentSelected == 'none')}
														<a class="link" href="{link application='ultimate' date=$date contentslug=$content->contentSlug}{/link}" itemprop="url">{$content->getTitle()}</a>
													{else}
														{$content->getTitle()}
													{/if}
												</h1>
											{/if}
											
											{if $requestType != 'page'}
												<p class="likeContainer">
											{/if}
											{if $contentMetaDisplaySelected[$requestType]|isset || $contentMetaDisplaySelected|count == 0}
												{hascontent}
													<span class="meta" id="metaAbove-{$contentID}">
													
														{content}
															{if $metaAbove[$contentID]|isset && $metaAbove[$contentID] != ""}
																{@$metaAbove[$contentID]}
															{/if}
															{if $metaAbove_i18n[$contentID][$__wcf->getLanguage()->__get('languageID')]|isset}
																{@$metaAbove_i18n[$contentID][$__wcf->getLanguage()->__get('languageID')]}
															{/if}
														{/content}
													
													</span>
												{/hascontent}
											{/if}
											{if $requestType != 'page'}
												</p>
											{/if}
										
										</div>
										
										{if $requestType == 'content'}
											{hascontent}
												<p class="abstract" itemprop="description">{content}{$content->contentDescription}{/content}</p>
											{/hascontent}
										{/if}
									</header>
									{if $block->contentBodyDisplay != 'hide'}
										<div class="messageBody">
											<div>
												{if !$pageNo|isset}{assign var=pageNo value=1}{/if}
												<div itemprop="articleBody" id="content-{$contentID}" 
													class="content htmlContent {implode from=$content->categories item=category glue=' '}category-{$category->categorySlug}{/implode} 
													{implode from=$content->tags[$__wcf->getLanguage()->__get('languageID')] item=tag glue=''}tag-{$tag->getTitle()}{/implode}
													messageText">
													
													{if ($block->contentBodyDisplay == 'default' && (($displayedFeaturedContents < $block->featuredContents && $pageNo == 1) || $requestType == 'content' || $requestType == 'page')) || $block->contentBodyDisplay == 'full'}
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
											</div>
											{assign var=objectID value=$contentID}
											{include file='attachments'}
											
											<div class="messageSignature">
												
												{if $contentMetaDisplaySelected[$requestType]|isset || $contentMetaDisplaySelected|count == 0}
													{hascontent}
														<div class="meta" id="metaBelow-{$contentID}">
															
															{content}
																{if $metaBelow[$contentID]|isset && $metaBelow[$contentID] != ""}
																	{@$metaBelow[$contentID]}
																{/if}
																{if $metaBelow_i18n[$contentID][$__wcf->getLanguage()->__get('languageID')]|isset}
																	{@$metaBelow_i18n[$contentID][$__wcf->getLanguage()->__get('languageID')]}
																{/if}
															{/content}
															
														</div>
													{/hascontent}
												{/if}
											
											</div>
											
											<div class="messageFooter">
											
											</div>
											
											<footer class="messageOptions">
												<nav class="jsMobileNavigation buttonGroupNavigation">
													<ul class="smallButtons buttonGroup">{*
														*}{if $requestType != 'content' && $requestType != 'page' && ($queryModeSelected == 'default' || $fetchPageContentSelected == 'none')}<li><a href="{link application='ultimate' date=$date contentslug=$content->contentSlug}{/link}" class="button"><span class="icon icon16 icon-arrow-right"></span> <span>{lang}wcf.global.button.readMore{/lang}</span></a></li>{/if}{*
														*}{if $__wcf->getSession()->getPermission('user.ultimate.content.canEditContent') && !$hideInlineEdit}<li><a title="{lang}wcf.acp.ultimate.content.edit{/lang}" class="button jsMessageEditButton"><span class="icon icon16 icon-pencil"></span> <span>{lang}wcf.global.button.edit{/lang}</span></a></li>{/if}{*
														*}<li class="toTopLink"><a href="{@$anchor}" title="{lang}wcf.global.scrollUp{/lang}" class="button jsTooltip"><span class="icon icon16 icon-arrow-up"></span> <span class="invisible">{lang}wcf.global.scrollUp{/lang}</span></a></li>{*
													*}</ul>
												</nav>
											</footer>
										</div>
									{/if}
								</div>
							</section>
						</div>
					{/if}
					{if $block->commentsVisibility == 'show' || ($block->commentsVisibility == 'auto' && $requestType == 'content')}
						{assign var='commentContainerID' value='content-'|concat:$contentID|concat:'-comments'}
						{assign var='commentList' value=$commentLists[$contentID]}
						{if MODULE_LIKE}
							{assign var=likeData value=$commentList->getLikeData()}
						{/if}
						
						<header id="comments" class="boxHeadline boxSubHeadline">
							<h2>
								{lang}ultimate.content.comments{/lang}
								<span class="badge">{$commentList->objects|count}</span>
							</h2>
						</header>
						<div class="container containerList marginTop content comments">
							
							{include file='__commentJavaScript' commentContainerID=$commentContainerID}
							
							{if $commentCanAdd[$contentID]}
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
										{lang}ultimate.content.noComments{/lang}
									</div>
								{/hascontent}
							{/if}
						</div>
					{/if}
				</article>
			</li>
		{*/if*}
	{/foreach}
	
		</ul>
	</div>
	
	{if $requestType == 'category'}
		<div class="contentNavigation">
			{@$pagesLinks}
		</div>
	{/if}
</div>
