<div id="block-{$blockID}" class="block block-type-content" data-height="{$height}">
	{assign var=displayedFeaturedContents value=0}
	{foreach from=$contents key=contentID item=content}
		{if $content->status == 3}
		<div itemtype="http://schema.org/Article" itemscope="">
			{if $block->showContent}
			<div id="content-{$contentID}" class="container containerPadding marginTop content {implode from=$content->categories item=category glue=' '}category-{$category->categorySlug}{/implode}  {implode from=$content->tags[$__wcf->getLanguage()->__get('languageID')] item=tag glue=''}tag-{$tag->getTitle()}{/implode}">
				<header class="boxHeadline">
					{if $block->showTitles}
						<h1 itemprop="name">
							{if $requestType != 'content'}
								<a href="{$readMoreLink[$contentID]}" itemprop="url">{lang}{$content->contentTitle}{/lang}</a>
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
				<p itemprop="articleBody">
					{if ($block->contentBodyDisplay == 'default' && ($displayedFeaturedContents < $block->featuredContents || $requestType == 'content' || $requestType == 'page')) || $block->contentBodyDisplay == 'full'}
						{counter name=displayedFeaturedContents assign=displayedFeaturedContents print=false start=0}
						{assign var=displayedFeaturedContents value=$displayedFeaturedContents}
						{if $requestType == 'content' || $requestType == 'page'}
							{@$content->getParsedContent()}
						{/if}
						{if $requestType == 'index' || $requestType == 'category'}
							{@$content->getParsedContent()|truncateMore:0}
						{/if}
					{else}
						{$content->getParsedContent()|truncateMore:ULTIMATE_GENERAL_CONTENT_CONTINUEREADINGLENGTH}
						
						<a href="{$readMoreLink[$contentID]}">{lang}{$readMoreText}{/lang}&nbsp;-&gt;</a>
					{/if}
				</p>
				{/if}
				
				{if $contentMetaDisplaySelected[$requestType]|isset || $contentMetaDisplaySelected|count == 0}
					<footer>
						{hascontent}
							<small class="meta" id="metaBelow-{$contentID}">
							{content}
								{if $metaBelow[$contentID]|isset && $metaBelow[$contentID] != ""}
									{@$metaBelow[$contentID]}
								{/if}
								{if $metaBelow_i18n[$contentID][$__wcf->getLanguage()->__get('languageID')]|isset}
									{@$metaBelow_i18n[$contentID][$__wcf->getLanguage()->__get('languageID')]}
								{/if}
							{/content}
							</small>
						{/hascontent}
					</footer>
				{/if}					
			</div>
			{/if}
			
			{if $block->commentsVisibility == 'show' || ($block->commentsVisibility == 'auto' && $requestType == 'content')}
			<div class="content">
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
			</div>
			{/if}
		</div>
		{/if}
	{/foreach}
</div>