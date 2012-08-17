<div id="mediaBlock-{$blockID}" class="block media-block-type">
	{if $mediaType == 'video'}
		{if $mediaSourceType == 'provider'}
			{@$mediaHTML}
		{else}
		<video controls="controls" preload="metadata" height="{$mediaHeight}" width="{$mediaWidth}">
			<source type="{$mediaMimeType->mimeType}" src="{@$mediaSource}" />
		</video>
		{/if}
	{/if}
	{if $mediaType == 'audio'}
		<audio controls="controls">
			<source type="{$mediaMimeType->mimeType}" src="{@$mediaSource}" />
		</audio>
	{/if}
</div>