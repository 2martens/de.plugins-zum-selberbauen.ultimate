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
						{if $content->enableHtml}
							{capture assign='contentText'}{lang}{@$content->contentText}{/lang}{/capture}
							{assign var='contentText' value=$contentText}
							{if $requestType == 'content' || $requestType == 'page'}
								{@$contentText}
							{/if}
							{if $requestType == 'index' || $requestType == 'category'}
								{@$contentText|truncateMore:0}
							{/if}
						{else}
							{capture assign='contentText'}{lang}{$content->contentText}{/lang}{/capture}
							{assign var='contentText' value=$contentText}
							{if $requestType == 'content' || $requestType == 'page'}
								{$contentText}
							{/if}
							{if $requestType == 'index' || $requestType == 'category'}
								{$contentText|truncateMore:0}
							{/if}
						{/if}
					{else}
						{if $content->enableHtml}
							{capture assign='languageText'}{lang}{@$content->contentText}{/lang}{/capture}
							{assign var='languageText' value=$languageText}
							{@$languageText|truncateMore:ULTIMATE_CONTENT_CONTINUE_READING_LENGTH}
						{else}
							{capture assign='languageText'}{lang}{$content->contentText}{/lang}{/capture}
							{assign var='languageText' value=$languageText}
							{$languageText|truncateMore:ULTIMATE_CONTENT_CONTINUE_READING_LENGTH}
						{/if}
						
						<a href="{link controller='' date=$content->publishDateObj->format('Y/m/d') contentSlug=$content->contentSlug}#more-{$content->contentID}{/link}">{lang}{$readMoreText}{/lang}&nbsp;-&gt;</a>
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
				{* TODO: comments *}
			</div>
			{/if}
		{/if}
	{/foreach}
</div>