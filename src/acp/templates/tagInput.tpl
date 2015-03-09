<dl id="tagContainer{if $tagInputSuffix|isset}{@$tagInputSuffix}{/if}" class="jsOnly tagContainer">
	<dt><label for="tagSearchInput{if $tagInputSuffix|isset}{@$tagInputSuffix}{/if}">{lang}wcf.tagging.tags{/lang}</label></dt>
	<dd>
		<div id="tagList{if $tagInputSuffix|isset}{@$tagInputSuffix}{/if}" class="editableItemList"></div>
	</dd>
</dl>
<script data-relocate="true" type="text/javascript">
	//<![CDATA[
	$(function() {
		{if $languageID|isset}
		var $tagList = new ULTIMATE.Tagging.TagList('#tagList{if $tagInputSuffix|isset}{@$tagInputSuffix}{/if}', '#tagSearchInput{if $tagInputSuffix|isset}{@$tagInputSuffix}{/if}', {@TAGGING_MAX_TAG_LENGTH}, {$languageID});
		{else}
		var $tagList = new WCF.Tagging.TagList('#tagList{if $tagInputSuffix|isset}{@$tagInputSuffix}{/if}', '#tagSearchInput{if $tagInputSuffix|isset}{@$tagInputSuffix}{/if}', {@TAGGING_MAX_TAG_LENGTH});
		{/if}
		{if $tags|isset && $tags|count}
			$tagList.load([ {implode from=$tags item=tag}'{$tag}'{/implode} ]);
		{/if}
	});
	//]]>
</script>
