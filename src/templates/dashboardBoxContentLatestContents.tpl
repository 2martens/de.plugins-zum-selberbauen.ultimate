<header class="boxHeadline boxSubHeadline">
	<h2>{lang}ultimate.content.contents{/lang}</h2>
</header>

<ul class="messageList">
	{assign var=displayedFeaturedContents value=0}
	{foreach from=$contents key=contentID item=content}
	{if $content->status == 3}
		<li>
			<article class="message messageReduced marginTop">
				<div>
					<section class="messageContent">
						<div>
							<header class="messageHeader">
								<div class="box32">
									{if $content->authorID}
										<a href="{link controller='User' object=$content->authorProfile}{/link}" class="framed">{@$content->authorProfile->getAvatar()->getImageTag(32)}</a>
									{else}
										<span class="framed">{@$content->authorProfile->getAvatar()->getImageTag(32)}</span>
									{/if}
									
									<div class="messageHeadline">
										<h1><a href="{link application='ultimate' controller='Content' date=$content->publishDateObject->format('Y-m-d') contentslug=$content->contentSlug}{/link}">{$content->getTitle()}</a></h1>
										<p>
											<span class="username">{if $content->authorID}<a href="{link controller='User' object=$content->authorProfile}{/link}" class="userLink" data-user-id="{@$content->authorID}">{$content->author->username}</a>{else}{$content->author->username}{/if}</span>
											<span>{@$content->publishDate|time}</span>{*
											
											*}{if MODULE_LIKE && $__wcf->getSession()->getPermission('user.like.canViewLike')}{if $content->likes || $content->dislikes}<span class="likesBadge badge jsTooltip {if $content->cumulativeLikes > 0}green{elseif $content->cumulativeLikes < 0}red{/if}" title="{lang likes=$content->likes dislikes=$content->dislikes}wcf.like.tooltip{/lang}">{if $content->cumulativeLikes > 0}+{elseif $content->cumulativeLikes == 0}&plusmn;{/if}{#$content->cumulativeLikes}</span>{/if}{/if}
										</p>
									</div>
								</div>
							</header>
							
							<div class="messageBody">
								<div>
									{if ($block->contentBodyDisplay == 'default' && ($displayedFeaturedContents < $block->featuredContents)) || $block->contentBodyDisplay == 'full'}
										{counter name=displayedFeaturedContents assign=displayedFeaturedContents print=false start=0}
										{assign var=displayedFeaturedContents value=($displayedFeaturedContents + 1)}
										<p>{@$content->getFormattedMessage()|truncateMore:0}</p>
									{else}
										<p>{@$content->getFormattedMessage()|truncateMore:ULTIMATE_GENERAL_CONTENT_CONTINUEREADINGLENGTH}</p>
									{/if}
								</div>
								
								<footer class="messageOptions">
									<nav class="jsMobileNavigation buttonGroupNavigation">
										<ul class="smallButtons buttonGroup">{*
											*}<li><a href="{link application='ultimate' controller='Content' date=$content->publishDateObject->format('Y-m-d') contentslug=$content->contentSlug}{/link}" class="button"><span class="icon icon16 icon-arrow-right"></span> <span>{lang}wcf.global.button.readMore{/lang}</span></a></li>{*
											*}<li class="toTopLink"><a href="{@$__wcf->getAnchor('top')}" title="{lang}wcf.global.scrollUp{/lang}" class="button jsTooltip"><span class="icon icon16 icon-arrow-up"></span> <span class="invisible">{lang}wcf.global.scrollUp{/lang}</span></a></li>{*
										*}</ul>
									</nav>
								</footer>
							</div>
						</div>
					</section>
				</div>
			</article>
		</li>
	{/if}
	{/foreach}
</ul>
