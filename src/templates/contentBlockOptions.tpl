<div id="block_{$blockID}_tab" class="tabMenuContainer containerPadding" data-is-parent="true" data-active="block_{$blockID}_tab_mode">
	{assign var='metaAboveContentID' value='metaAboveContent_'|concat:$blockID}
	{assign var='metaBelowContentID' value='metaBelowContent_'|concat:$blockID}
	
	{include file='multipleLanguageInputJavascript' elementIdentifier=$metaAboveContentID forceSelection=false}
	{include file='multipleLanguageInputJavascript' elementIdentifier=$metaBelowContentID forceSelection=false}
	
	<form method="post" action="">
		<nav class="tabMenu">
			<ul>
				{assign var='modeTab' value='block_'|concat:$blockID|concat:'_tab_mode'}
				{assign var='queryTab' value='block_'|concat:$blockID|concat:'_tab_query'}
				{assign var='displayTab' value='block_'|concat:$blockID|concat:'_tab_display'}
				{assign var='metaTab' value='block_'|concat:$blockID|concat:'_tab_meta'}
				<li data-menu-item="block_{$blockID}_tab_mode"><a href="{$__wcf->getAnchor($modeTab)}" title="{lang}wcf.acp.ultimate.template.contentTab.mode{/lang}">{lang}wcf.acp.ultimate.template.contentTab.mode{/lang}</a></li>
				<li data-menu-item="block_{$blockID}_tab_query"><a href="{$__wcf->getAnchor($queryTab)}" title="{lang}wcf.acp.ultimate.template.contentTab.query{/lang}">{lang}wcf.acp.ultimate.template.contentTab.query{/lang}</a></li>
				<li data-menu-item="block_{$blockID}_tab_display"><a href="{$__wcf->getAnchor($displayTab)}" title="{lang}wcf.acp.ultimate.template.contentTab.display{/lang}">{lang}wcf.acp.ultimate.template.contentTab.display{/lang}</a></li>
				<li data-menu-item="block_{$blockID}_tab_meta"><a href="{$__wcf->getAnchor($metaTab)}" title="{lang}wcf.acp.ultimate.template.contentTab.meta{/lang}">{lang}wcf.acp.ultimate.template.contentTab.meta{/lang}</a></li>
			</ul>
		</nav>
		
		<div id="block_{$blockID}_tab_mode" class="container tabMenuContent containerPadding" data-parent-menu-item="block_{$blockID}_tab_mode">
			<div class="info">
				<p>{lang}wcf.acp.ultimate.template.contentTab.mode.info{/lang}</p>
			</div>
			<dl class="wide">
				<dd class="inputSelect">
					<label for="queryMode_{$blockID}">{lang}wcf.acp.ultimate.template.contentTab.mode.queryMode{/lang}</label>
					
					<select id="queryMode_{$blockID}" name="queryMode" data-block-id="{$blockID}" data-is-block="true">
						<option value="default"{if $queryModeSelected == 'default'} selected="selected"{/if}>{lang}wcf.acp.ultimate.template.contentTab.mode.queryMode.default{/lang}</option>
						<option value="custom"{if $queryModeSelected == 'custom'} selected="selected"{/if}>{lang}wcf.acp.ultimate.template.contentTab.mode.queryMode.custom{/lang}</option>
					</select>
					<script data-relocate="true" type="text/javascript">
					//<![CDATA[
						$(function() {
							$('#queryMode_{$blockID}').change(function(event) {
								var $target = $(event.currentTarget);
								var $value = $target.val();
								
								if ($value == 'default') {
									$('#block_{$blockID}_tab_query').find('select', 'input', 'button')
										.addClass('disabled').prop('disabled', true);
									$('#block_{$blockID}_tab_query').find('input', 'textarea').prop('readonly', true);
								} else {
									$('#block_{$blockID}_tab_query').find('select', 'input', 'button')
										.removeClass('disabled').prop('disabled', false).prop('readonly', false);
									$('#block_{$blockID}_tab_query').find('input', 'textarea').prop('readonly', false);
								}
							});
							
							var $target = $('#queryMode_{$blockID}');
							var $value = $target.val();
								
							if ($value == 'default') {
								$('#block_{$blockID}_tab_query').find('select', 'input', 'button')
									.addClass('disabled').prop('disabled', true);
								$('#block_{$blockID}_tab_query').find('input', 'textarea').prop('readonly', true);
							}
						});
					//]]>
					</script>
				</dd>
			</dl>
		</div>
		<div id="block_{$blockID}_tab_query" class="container tabMenuContent containerPadding" data-parent-menu-item="block_{$blockID}_tab_query">
			<div class="info">
				<p>{lang}wcf.acp.ultimate.template.contentTab.query.info{/lang}</p>
			</div>
			<dl class="wide">
				<dd class="inputSelect">
					<label for="fetchPageContent_{$blockID}" class="jsTooltip" title="{lang}wcf.acp.ultimate.template.contentTab.query.fetchPageContent.description{/lang}">{lang}wcf.acp.ultimate.template.contentTab.query.fetchPageContent{/lang}</label>
					
					<select id="fetchPageContent_{$blockID}" name="fetchPageContent" data-block-id="{$blockID}" data-is-block="true">
						<option value="none"{if $fetchPageContentSelected == 'none'} selected="selected"{/if}>{lang}wcf.acp.ultimate.template.contentTab.query.fetchPageContent.none{/lang}</option>
						{foreach from=$pages key=pageID item=page}
						<option value="{$pageID}"{if $fetchPageContentSelected == $pageID} selected="selected"{/if}>{$page}</option>
						{/foreach}
					</select>
				</dd>
				<dd class="inputMultiSelect">
					<label for="categories_{$blockID}" class="jsTooltip" title="{lang}wcf.acp.ultimate.template.contentTab.query.categories.description{/lang}">{lang}wcf.acp.ultimate.template.contentTab.query.categories{/lang}</label>
					
					<select id="categories_{$blockID}" name="categories" data-block-id="{$blockID}" data-is-block="true" multiple="multiple">
						{foreach from=$categories key=categoryID item=category}
						<option value="{$categoryID}"{if $categoriesSelected[$categoryID]|isset} selected="selected"{/if}>{@$category}</option>
						{/foreach}
					</select>
				</dd>
				<dd class="inputSelect">
					<label for="categoryMode_{$blockID}" class="jsTooltip" title="{lang}wcf.acp.ultimate.template.contentTab.query.categoryMode.description{/lang}">{lang}wcf.acp.ultimate.template.contentTab.query.categoryMode{/lang}</label>
					
					<select id="categoryMode_{$blockID}" name="categoryMode" data-block-id="{$blockID}" data-is-block="true">
						<option value="include"{if $categoryModeSelected == 'include'} selected="selected"{/if}>{lang}wcf.acp.ultimate.template.contentTab.query.categoryMode.include{/lang}</option>
						<option value="exclude"{if $categoryModeSelected == 'exclude'} selected="selected"{/if}>{lang}wcf.acp.ultimate.template.contentTab.query.categoryMode.exclude{/lang}</option>
					</select>
				</dd>
				<dd class="inputMultiSelect">
					<label for="authors_{$blockID}" class="jsTooltip" title="{lang}wcf.acp.ultimate.template.contentTab.query.authors.description{/lang}">{lang}wcf.acp.ultimate.template.contentTab.query.authors{/lang}</label>
					
					<select id="authors_{$blockID}" name="authors" data-block-id="{$blockID}" data-is-block="true" multiple="multiple">
						{foreach from=$authors key=authorID item=author}
						<option value="{$authorID}"{if $authorsSelected[$authorID]|isset} selected="selected"{/if}>{@$author->getTitle()}</option>
						{/foreach}
					</select>
				</dd>
				<dd class="inputInteger">
					<label for="numberOfContents_{$blockID}" class="jsTooltip" title="{lang}wcf.acp.ultimate.template.contentTab.query.numberOfContents.description{/lang}">{lang}wcf.acp.ultimate.template.contentTab.query.numberOfContents{/lang}</label>
					<input type="number" step="1" min="1" max="{$contents|count}" id="numberOfContents_{$blockID}" name="numberOfContents" value="{$numberOfContents}" data-block-id="{$blockID}" data-is-block="true" />
				</dd>
				<dd class="inputInteger">
					<label for="offset_{$blockID}" class="jsTooltip" title="{lang}wcf.acp.ultimate.template.contentTab.query.offset.description{/lang}">{lang}wcf.acp.ultimate.template.contentTab.query.offset{/lang}</label>
					<input type="number" step="1" min="0" max="{$contents|count - 1}" id="offset_{$blockID}" name="offset" value="{$offset}" data-block-id="{$blockID}" data-is-block="true" />
				</dd>
				<dd class="inputSelect">
					<label for="sortField_{$blockID}">{lang}wcf.acp.ultimate.template.contentTab.query.sortField{/lang}</label>
					
					<select id="sortField_{$blockID}" name="sortField" data-block-id="{$blockID}" data-is-block="true">
						<option value="contentID"{if $sortFieldSelected == 'contentID'} selected="selected"{/if}>{lang}wcf.global.objectID{/lang}</option>
						<option value="contentTitle"{if $sortFieldSelected == 'contentTitle'} selected="selected"{/if}>{lang}wcf.acp.ultimate.content.title{/lang}</option>
						<option value="contentAuthor"{if $sortFieldSelected == 'contentAuthor'} selected="selected"{/if}>{lang}wcf.acp.ultimate.author{/lang}</option>
						<option value="lastModified"{if $sortFieldSelected == 'lastModified'} selected="selected"{/if}>{lang}wcf.acp.ultimate.lastModified{/lang}</option>
					</select>
				</dd>
				<dd class="inputSelect">
					<label for="sortOrder_{$blockID}">{lang}wcf.acp.ultimate.template.contentTab.query.sortOrder{/lang}</label>
					
					<select id="sortOrder_{$blockID}" name="sortOrder" data-block-id="{$blockID}" data-is-block="true">
						<option value="ASC"{if $sortOrderSelected == 'ASC'} selected="selected"{/if}>{lang}wcf.global.sortOrder.ascending{/lang}</option>
						<option value="DESC"{if $sortOrderSelected == 'DESC'} selected="selected"{/if}>{lang}wcf.global.sortOrder.descending{/lang}</option>
					</select>
				</dd>
			</dl>
		</div>
		<div id="block_{$blockID}_tab_display" class="container tabMenuContent containerPadding" data-parent-menu-item="block_{$blockID}_tab_display">
			<dl class="wide">
				<dd class="inputCheckbox">
					<label for="hideTitles_{$blockID}" class="jsTooltip" title="{lang}wcf.acp.ultimate.template.contentTab.display.hideTitles.description{/lang}">{lang}wcf.acp.ultimate.template.contentTab.display.hideTitles{/lang}</label>
					<input type="checkbox" id="hideTitles_{$blockID}" name="hideTitles" value="{if $hideTitles}1{else}0{/if}" data-block-id="{$blockID}" data-is-block="true"{if $hideTitles} checked="checked"{/if} />
				</dd>
				<dd class="inputSelect">
					<label for="contentBodyDisplay_{$blockID}" class="jsTooltip" title="{lang}wcf.acp.ultimate.template.contentTab.display.contentBodyDisplay.description{/lang}">{lang}wcf.acp.ultimate.template.contentTab.display.contentBodyDisplay{/lang}</label>
					
					<select id="contentBodyDisplay_{$blockID}" name="contentBodyDisplay" data-block-id="{$blockID}" data-is-block="true">
						<option value="default"{if $contentBodyDisplaySelected == 'default'} selected="selected"{/if}>{lang}wcf.acp.ultimate.template.contentTab.display.contentBodyDisplay.default{/lang}</option>
						<option value="full"{if $contentBodyDisplaySelected == 'full'} selected="selected"{/if}>{lang}wcf.acp.ultimate.template.contentTab.display.contentBodyDisplay.full{/lang}</option>
						<option value="excerpt"{if $contentBodyDisplaySelected == 'excerpt'} selected="selected"{/if}>{lang}wcf.acp.ultimate.template.contentTab.display.contentBodyDisplay.excerpt{/lang}</option>
						<option value="hide"{if $contentBodyDisplaySelected == 'hide'} selected="selected"{/if}>{lang}wcf.acp.ultimate.template.contentTab.display.contentBodyDisplay.hide{/lang}</option>
					</select>
				</dd>
				<dd class="inputCheckbox">
					<label for="hideContent_{$blockID}" class="jsTooltip" title="{lang}wcf.acp.ultimate.template.contentTab.display.hideContent.description{/lang}">{lang}wcf.acp.ultimate.template.contentTab.display.hideContent{/lang}</label>
					<input type="checkbox" id="hideContent_{$blockID}" name="hideContent" value="{if $hideContent}1{else}0{/if}" data-block-id="{$blockID}" data-is-block="true"{if $hideContent} checked="checked"{/if} />
				</dd>
				<dd class="inputSelect">
					<label for="commentsVisibility_{$blockID}" class="jsTooltip" title="{lang}wcf.acp.ultimate.template.contentTab.display.commentsVisibility.description{/lang}">{lang}wcf.acp.ultimate.template.contentTab.display.commentsVisibility{/lang}</label>
					
					<select id="commentsVisibility_{$blockID}" name="commentsVisibility" data-block-id="{$blockID}" data-is-block="true">
						<option value="auto"{if $commentsVisibilitySelected == 'auto'} selected="selected"{/if}>{lang}wcf.acp.ultimate.template.contentTab.display.commentsVisibility.auto{/lang}</option>
						<option value="hide"{if $commentsVisibilitySelected == 'hide'} selected="selected"{/if}>{lang}wcf.acp.ultimate.template.contentTab.display.commentsVisibility.hide{/lang}</option>
						<option value="show"{if $commentsVisibilitySelected == 'show'} selected="selected"{/if}>{lang}wcf.acp.ultimate.template.contentTab.display.commentsVisibility.show{/lang}</option>
					</select>
				</dd>
				<dd class="inputInteger">
					<label for="featuredContents_{$blockID}" class="jsTooltip" title="{lang}wcf.acp.ultimate.template.contentTab.display.featuredContents.description{/lang}">{lang}wcf.acp.ultimate.template.contentTab.display.featuredContents{/lang}</label>
					<input type="number" min="0" max="100" step="1" id="featuredContents_{$blockID}" name="featuredContents" value="{$featuredContents}" data-block-id="{$blockID}" data-is-block="true" />
				</dd>
				<dd class="inputCheckbox">
					<label for="hideInlineEdit_{$blockID}" class="jsTooltip" title="{lang}wcf.acp.ultimate.template.contentTab.display.hideInlineEdit.description{/lang}">{lang}wcf.acp.ultimate.template.contentTab.display.hideInlineEdit{/lang}</label>
					<input type="checkbox" id="hideInlineEdit_{$blockID}" name="hideInlineEdit" value="{if $hideInlineEdit}1{else}0{/if}" data-block-id="{$blockID}" data-is-block="true"{if $hideInlineEdit} checked="checked"{/if} />
				</dd>
			</dl>
		</div>
		<div id="block_{$blockID}_tab_meta" class="container tabMenuContent containerPadding" data-parent-menu-item="block_{$blockID}_tab_meta">
			<div class="info">
				<p>{lang}wcf.acp.ultimate.template.contentTab.meta.info{/lang}</p>
			</div>
			<dl class="wide">
				<dd class="inputMultiSelect">
					<label for="contentMetaDisplay_{$blockID}" class="jsTooltip" title="{lang}wcf.acp.ultimate.template.contentTab.meta.contentMetaDisplay.description{/lang}">{lang}wcf.acp.ultimate.template.contentTab.meta.contentMetaDisplay{/lang}</label>
					
					<select id="contentMetaDisplay_{$blockID}" name="contentMetaDisplay" data-block-id="{$blockID}" data-is-block="true" multiple="multiple">
						<option value="category"{if $contentMetaDisplaySelected['category']|isset} selected="selected"{/if}>{lang}wcf.acp.ultimate.template.contentTab.meta.contentMetaDisplay.category{/lang}</option>
						<option value="page"{if $contentMetaDisplaySelected['page']|isset} selected="selected"{/if}>{lang}wcf.acp.ultimate.template.contentTab.meta.contentMetaDisplay.page{/lang}</option>
						<option value="content"{if $contentMetaDisplaySelected['content']|isset} selected="selected"{/if}>{lang}wcf.acp.ultimate.template.contentTab.meta.contentMetaDisplay.content{/lang}</option>
					</select>
				</dd>
				<dd class="inputTextarea">
					<label for="metaAboveContent_{$blockID}" class="jsTooltip" title="{lang}wcf.acp.ultimate.template.contentTab.meta.metaAboveContent.description{/lang}">{lang}wcf.acp.ultimate.template.contentTab.meta.metaAboveContent{/lang}</label>
					<textarea id="metaAboveContent_{$blockID}" name="metaAboveContent" cols="40" rows="10" data-block-id="{$blockID}" data-is-block="true">{@$i18nPlainValues[$metaAboveContentID]}</textarea>
				</dd>
				<dd class="inputTextarea">
					<label for="metaBelowContent_{$blockID}" class="jsTooltip" title="{lang}wcf.acp.ultimate.template.contentTab.meta.metaBelowContent.description{/lang}">{lang}wcf.acp.ultimate.template.contentTab.meta.metaBelowContent{/lang}</label>
					<textarea id="metaBelowContent_{$blockID}" name="metaBelowContent" cols="40" rows="10" data-block-id="{$blockID}" data-is-block="true">{@$i18nPlainValues[$metaBelowContentID]}</textarea>
				</dd>
			</dl>
		</div>
		<div class="formSubmit">
			<input type="submit" id="blockSubmitButton" name="submitButton" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		</div>
	</form>
</div>