<div id="contentBlock-{$blockID}" class="block block-type-content">
	{foreach from=$contents key=contentID item=content}
		{if $content->status == 3}
			<div id="content-{$contentID}" class="content {implode from=$content->categories item=category glue=' '}category-{$category->categorySlug}{/implode}  {implode from=$content->tags[$__wcf->getLanguage()->getLanguageID()] item=tag glue=''}tag-{$tag->getTitle()}{/implode}">
				<header>
					<h2 class="title">
						{if $requestType != 'content'}
							<a href="{link controller='' date=$content->publishDateObj->format('Y/m/d') contentSlug=$content->contentSlug}{/link}">{lang}{$content->contentTitle}{/lang}</a>
						{else}
							{lang}{$content->contentTitle}{/lang}	
						{/if}
					</h2>
					{if $requestType == 'content'}
						<p class="abstract">{lang}{$content->contentDescription}{/lang}</p>
					{/if}
				</header>
				<p>
					{if $requestType == 'content'}
						{if $content->enableHtml}
							{lang}{@$content->contentText}{/lang}
						{else}
							{lang}{$content->contentText}{/lang}
						{/if}
					{else}
						{if $content->enableHtml}
							{capture assign='languageText'}{lang}{@$content->contentText}{/lang}{/capture}
							{assign var='languageText' value=$languageText}
							{@$languageText|truncateMore:ULTIMATE_CONTENT_CONTINUE_READING_LENGTH:'...'}
						{else}
							{capture assign='languageText'}{lang}{@$content->contentText}{/lang}{/capture}
							{assign var='languageText' value=$languageText}
							{$languageText|truncateMore:ULTIMATE_CONTENT_CONTINUE_READING_LENGTH:'...'}
						{/if}
						
						<a href="{link controller='' date=$content->publishDateObj->format('Y/m/d') contentSlug=$content->contentSlug}#more-{$content->contentID}{/link}">{lang}ultimate.content.continueReading{/lang}&nbsp;-&gt;</a>
					{/if}
				</p>
			</div>
		{/if}
	{/foreach}
</div>