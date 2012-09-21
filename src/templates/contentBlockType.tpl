<div{if !$visualEditorMode} id="block-{$blockID}" class="block block-type-content" data-width="{$width}" data-height="{$height}" data-left="{$left}" data-top="{$top}"{/if}>
	{assign var=displayedFeaturedContents value=0}
	{foreach from=$contents key=contentID item=content}
		{if $content->status == 3 || $visualEditorMode|isset}
			{if $block->showContent}
			<div id="content-{$contentID}" class="content {implode from=$content->categories item=category glue=' '}category-{$category->categorySlug}{/implode}  {implode from=$content->tags[$__wcf->getLanguage()->getLanguageID()] item=tag glue=''}tag-{$tag->getTitle()}{/implode}">
				<header>
					{if $block->showTitles}
					<h2 class="title">
						{if $requestType != 'content'}
							<a href="{link controller='' date=$content->publishDateObj->format('Y/m/d') contentSlug=$content->contentSlug}{/link}">{lang}{$content->contentTitle}{/lang}</a>
						{else}
							{lang}{$content->contentTitle}{/lang}	
						{/if}
					</h2>
					{/if}
					{if $requestType == 'content'}
						{hascontent}
							<p class="abstract">{content}{lang}{$content->contentDescription}{/lang}{/content}</p>
						{/hascontent}
					{/if}
					{if $contentMetaDisplaySelected[$requestType]|isset || $contentMetaDisplaySelected|count == 0}
						{hascontent}
							<p id="metaAbove-{$contentID}">{content}{$metaAbove[$contentID]}{/content}</p>
						{/hascontent}
					{/if}
				</header>
				{if $block->contentBodyDisplay != 'hide'}
				<p>
					{if ($block->contentBodyDisplay == 'default' && ($displayedFeaturedContents < $block->featuredContents || $requestType == 'content' || $requestType == 'page')) || $block->contentBodyDisplay == 'full'}
						{counter name=displayedFeaturedContents assign=displayedFeaturedContents print=false start=0}
						{assign var=displayedFeaturedContents value=$displayedFeaturedContents}
						{if $requestType == 'content' || $requestType == 'page'}
							{$content->getParsedContent()}
						{/if}
						{if $requestType == 'index' || $requestType == 'category'}
							{$content->getParsedContent()|truncateMore:0}
						{/if}
					{else}
						{$content->getParsedContent()|truncateMore:ULTIMATE_GENERAL_CONTENT_CONTINUEREADINGLENGTH}
						
						<a href="{link controller='' date=$content->publishDateObj->format('Y/m/d') contentSlug=$content->contentSlug}#more{/link}">{lang}{$readMoreText}{/lang}&nbsp;-&gt;</a>
					{/if}
				</p>
				{/if}
				{if $contentMetaDisplaySelected[$requestType]|isset || $contentMetaDisplaySelected|count == 0}
					<footer>
						{hascontent}
							<p id="metaBelow-{$contentID}">{content}{$metaBelow[$contentID]}{/content}</p>
						{/hascontent}
					</footer>
				{/if}					
			</div>
			{/if}
			{if $block->commentsVisibility == 'show' || ($block->commentsVisibility == 'auto' && $requestType == 'content')}
			<div id="content-{$contentID}-comments" class="content">
				{include file='__commentJavaScript'}

				<ul data-object-id="{@$contentID}" data-object-type-id="{@$commentObjectTypeID}" class="commentList containerList">
					{include file='commentList' commentList=$commentLists[$contentID]}
				</ul>
			</div>
			{/if}
		{/if}
	{/foreach}
</div>