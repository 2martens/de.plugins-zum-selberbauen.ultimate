<div id="block-{$blockID}" class="block block-type-content" data-height="{$height}">
	{assign var=displayedFeaturedContents value=0}
	{foreach from=$contents key=contentID item=content}
		{if $content->status == 3}
		<article itemtype="http://schema.org/Article" itemscope="" class="container containerPadding marginTop">
			{if !$block->hideContent}
				<header class="boxHeadline">
					{if !$block->hideTitles}
						<h1 itemprop="name">
							{if $requestType != 'content'}
								<a class="link" href="{$readMoreLink[$contentID]}" itemprop="url">{lang}{$content->contentTitle}{/lang}</a>
							{else}
								{lang}{$content->contentTitle}{/lang}	
							{/if}
						</h1>
					{/if}
					
					{if $contentMetaDisplaySelected[$requestType]|isset || $contentMetaDisplaySelected|count == 0}
						{hascontent}
							<small class="meta" id="metaAbove-{$contentID}">
							{content}
								{if $metaAbove[$contentID]|isset && $metaAbove[$contentID] != ""}
									{@$metaAbove[$contentID]}
								{/if}
								{if $metaAbove_i18n[$contentID][$__wcf->getLanguage()->__get('languageID')]|isset}
									{@$metaAbove_i18n[$contentID][$__wcf->getLanguage()->__get('languageID')]}
								{/if}
							{/content}
							</small>
						{/hascontent}
					{/if}
					{if $requestType == 'content'}
						{hascontent}
							<p class="abstract" itemprop="description">{content}{lang}{$content->contentDescription}{/lang}{/content}</p>
						{/hascontent}
					{/if}
				</header>
				{if $block->contentBodyDisplay != 'hide'}
					<div itemprop="articleBody" id="content-{$contentID}" class="content {implode from=$content->categories item=category glue=' '}category-{$category->categorySlug}{/implode}  {implode from=$content->tags[$__wcf->getLanguage()->__get('languageID')] item=tag glue=''}tag-{$tag->getTitle()}{/implode}">
						{if ($block->contentBodyDisplay == 'default' && ($displayedFeaturedContents < $block->featuredContents || $requestType == 'content' || $requestType == 'page')) || $block->contentBodyDisplay == 'full'}
							{counter name=displayedFeaturedContents assign=displayedFeaturedContents print=false start=0}
							{assign var=displayedFeaturedContents value=$displayedFeaturedContents}
							{if $requestType == 'content' || $requestType == 'page'}
								{*if !$content->__get('enableHtml')}<p>{/if*}{@$content->getParsedContent()}{*if !$content->__get('enableHtml')}</p>{/if*}
							{/if}
							{if $requestType == 'index' || $requestType == 'category'}
								<p>{@$content->getParsedContent()|truncateMore:0}</p>
							{/if}
						{else}
							<p>{$content->getParsedContent()|truncateMore:ULTIMATE_GENERAL_CONTENT_CONTINUEREADINGLENGTH}</p>
								
							<a href="{$readMoreLink[$contentID]}">{lang}{$readMoreText}{/lang}&nbsp;-&gt;</a>
						{/if}
					</div>
				{/if}
				
				{if $contentMetaDisplaySelected[$requestType]|isset || $contentMetaDisplaySelected|count == 0}
					{hascontent}
						<aside class="meta" id="metaBelow-{$contentID}">
						{content}
							{if $metaBelow[$contentID]|isset && $metaBelow[$contentID] != ""}
								{@$metaBelow[$contentID]}
							{/if}
							{if $metaBelow_i18n[$contentID][$__wcf->getLanguage()->__get('languageID')]|isset}
								{@$metaBelow_i18n[$contentID][$__wcf->getLanguage()->__get('languageID')]}
							{/if}
						{/content}
						</aside>
					{/hascontent}
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
		{/if}
	{/foreach}
</div>