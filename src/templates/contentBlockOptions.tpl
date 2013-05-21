<div id="block_{$blockID}_tab" class="tabMenuContainer containerPadding" data-is-parent="true" data-active="block_{$blockID}_tab_mode">
	{assign var='readMoreTextID' value='readMoreText_'|concat:$blockID}
	{assign var='metaAboveContentID' value='metaAboveContent_'|concat:$blockID}
	{assign var='metaBelowContentID' value='metaBelowContent_'|concat:$blockID}
	
	{include file='multipleLanguageInputJavascript' elementIdentifier=$readMoreTextID forceSelection=false}
	{include file='multipleLanguageInputJavascript' elementIdentifier=$metaAboveContentID forceSelection=false}
	{include file='multipleLanguageInputJavascript' elementIdentifier=$metaBelowContentID forceSelection=false}
	
	<form method="post" action="">
	<nav class="tabMenu">
		<ul>
			{assign var='modeTab' value='block_'|concat:$blockID|concat:'_tab_mode'}
			{assign var='queryTab' value='block_'|concat:$blockID|concat:'_tab_query'}
			{assign var='displayTab' value='block_'|concat:$blockID|concat:'_tab_display'}
			{assign var='metaTab' value='block_'|concat:$blockID|concat:'_tab_meta'}
			<li data-menu-item="block_{$blockID}_tab_mode"><a href="{$__wcf->getAnchor($modeTab)}" title="{lang}ultimate.visualEditor.contentTab.mode{/lang}">{lang}ultimate.visualEditor.contentTab.mode{/lang}</a></li>
			<li data-menu-item="block_{$blockID}_tab_query"><a href="{$__wcf->getAnchor($queryTab)}" title="{lang}ultimate.visualEditor.contentTab.query{/lang}">{lang}ultimate.visualEditor.contentTab.query{/lang}</a></li>
			<li data-menu-item="block_{$blockID}_tab_display"><a href="{$__wcf->getAnchor($displayTab)}" title="{lang}ultimate.visualEditor.contentTab.display{/lang}">{lang}ultimate.visualEditor.contentTab.display{/lang}</a></li>
			<li data-menu-item="block_{$blockID}_tab_meta"><a href="{$__wcf->getAnchor($metaTab)}" title="{lang}ultimate.visualEditor.contentTab.meta{/lang}">{lang}ultimate.visualEditor.contentTab.meta{/lang}</a></li>
		</ul>
	</nav>
	
	<div id="block_{$blockID}_tab_mode" class="container tabMenuContent containerPadding" data-parent-menu-item="block_{$blockID}_tab_mode">
			<div class="info">
				<p>{lang}ultimate.visualEditor.contentTab.mode.info{/lang}</p>
			</div>
			<dl class="wide">
				<dd class="inputSelect">
					<label for="queryMode_{$blockID}">{lang}ultimate.visualEditor.contentTab.mode.queryMode{/lang}</label>
					
					<select id="queryMode_{$blockID}" name="queryMode" data-block-id="{$blockID}" data-is-block="true">
						<option value="default"{if $queryModeSelected == 'default'} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.mode.queryMode.default{/lang}</option>
						<option value="custom"{if $queryModeSelected == 'custom'} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.mode.queryMode.custom{/lang}</option>
					</select>
				</dd>
			</dl>
		</div>
		<div id="block_{$blockID}_tab_query" class="container tabMenuContent containerPadding" data-parent-menu-item="block_{$blockID}_tab_query">
			<div class="info">
				<p>{lang}ultimate.visualEditor.contentTab.query.info{/lang}</p>
			</div>
			<dl class="wide">
				<dd class="inputSelect">
					<label for="fetchPageContent_{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.query.fetchPageContent.description{/lang}">{lang}ultimate.visualEditor.contentTab.query.fetchPageContent{/lang}</label>
					
					<select id="fetchPageContent_{$blockID}" name="fetchPageContent" data-block-id="{$blockID}" data-is-block="true">
						<option value="none"{if $fetchPageContentSelected == 'none'} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.query.fetchPageContent.none{/lang}</option>
						{foreach from=$pages key=pageID item=page}
						<option value="{$pageID}"{if $fetchPageContentSelected == $pageID} selected="selected"{/if}>{$page}</option>
						{/foreach}
					</select>
				</dd>
				<dd class="inputMultiSelect">
					<label for="categories_{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.query.categories.description{/lang}">{lang}ultimate.visualEditor.contentTab.query.categories{/lang}</label>
					
					<select id="categories_{$blockID}" name="categories" data-block-id="{$blockID}" data-is-block="true" multiple="multiple">
						{foreach from=$categories key=categoryID item=category}
						<option value="{$categoryID}"{if $categoriesSelected[$categoryID]|isset} selected="selected"{/if}>{@$category}</option>
						{/foreach}
					</select>
				</dd>
				<dd class="inputSelect">
					<label for="categoryMode_{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.query.categoryMode.description{/lang}">{lang}ultimate.visualEditor.contentTab.query.categoryMode{/lang}</label>
					
					<select id="categoryMode_{$blockID}" name="categoryMode" data-block-id="{$blockID}" data-is-block="true">
						<option value="include"{if $categoryModeSelected == 'include'} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.query.categoryMode.include{/lang}</option>
						<option value="exclude"{if $categoryModeSelected == 'exclude'} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.query.categoryMode.exclude{/lang}</option>
					</select>
				</dd>
				<dd class="inputMultiSelect">
					<label for="authors_{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.query.authors.description{/lang}">{lang}ultimate.visualEditor.contentTab.query.authors{/lang}</label>
					
					<select id="authors_{$blockID}" name="authors" data-block-id="{$blockID}" data-is-block="true" multiple="multiple">
						{foreach from=$authors key=authorID item=author}
						<option value="{$authorID}"{if $authorsSelected[$authorID]|isset} selected="selected"{/if}>{@$author->getTitle()}</option>
						{/foreach}
					</select>
				</dd>
				<dd class="inputInteger">
					<label for="numberOfContents_{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.query.numberOfContents.description{/lang}">{lang}ultimate.visualEditor.contentTab.query.numberOfContents{/lang}</label>
					<input type="number" step="1" min="1" max="{$contents|count}" id="numberOfPosts_{$blockID}" name="numberOfContents" value="{$numberOfContents}" data-block-id="{$blockID}" data-is-block="true" />
				</dd>
				<dd class="inputInteger">
					<label for="offset_{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.query.offset.description{/lang}">{lang}ultimate.visualEditor.contentTab.query.offset{/lang}</label>
					<input type="number" step="1" min="0" max="{$contents|count - 1}" id="offset_{$blockID}" name="offset" value="{$offset}" data-block-id="{$blockID}" data-is-block="true" />
				</dd>
				<dd class="inputSelect">
					<label for="sortField_{$blockID}">{lang}ultimate.visualEditor.contentTab.query.sortField{/lang}</label>
					
					<select id="sortField_{$blockID}" name="sortField" data-block-id="{$blockID}" data-is-block="true">
						<option value="contentID"{if $sortFieldSelected == 'contentID'} selected="selected"{/if}>{lang}wcf.global.objectID{/lang}</option>
						<option value="contentTitle"{if $sortFieldSelected == 'contentTitle'} selected="selected"{/if}>{lang}wcf.acp.ultimate.content.title{/lang}</option>
						<option value="contentAuthor"{if $sortFieldSelected == 'contentAuthor'} selected="selected"{/if}>{lang}wcf.acp.ultimate.author{/lang}</option>
						<option value="lastModified"{if $sortFieldSelected == 'lastModified'} selected="selected"{/if}>{lang}wcf.acp.ultimate.lastModified{/lang}</option>
					</select>
				</dd>
				<dd class="inputSelect">
					<label for="sortOrder_{$blockID}">{lang}ultimate.visualEditor.contentTab.query.sortOrder{/lang}</label>
					
					<select id="sortOrder_{$blockID}" name="sortOrder" data-block-id="{$blockID}" data-is-block="true">
						<option value="ASC"{if $sortOrderSelected == 'ASC'} selected="selected"{/if}>{lang}wcf.global.sortOrder.ascending{/lang}</option>
						<option value="DESC"{if $sortOrderSelected == 'DESC'} selected="selected"{/if}>{lang}wcf.global.sortOrder.descending{/lang}</option>
					</select>
				</dd>
			</dl>
		</div>
		<div id="block_{$blockID}_tab_display" class="container tabMenuContent containerPadding" data-parent-menu-item="block_{$blockID}_tab_display">
			<dl class="wide">
				<dd class="inputText">
					<label for="readMoreText_{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.display.readMoreText.description{/lang}">{lang}ultimate.visualEditor.contentTab.display.readMoreText{/lang}</label>
					<input type="text" id="readMoreText_{$blockID}" name="readMoreText" value="{@$i18nPlainValues[$readMoreTextID]}" data-block-id="{$blockID}" data-is-block="true" />
				</dd>
				<dd class="inputCheckbox">
					<label for="showTitles_{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.display.showTitles.description{/lang}">{lang}ultimate.visualEditor.contentTab.display.showTitles{/lang}</label>
					<input type="checkbox" id="showTitles_{$blockID}" name="showTitles" value="{$showTitles}" data-block-id="{$blockID}" data-is-block="true" />
				</dd>
				<dd class="inputSelect">
					<label for="contentBodyDisplay_{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.display.contentBodyDisplay.description{/lang}">{lang}ultimate.visualEditor.contentTab.display.contentBodyDisplay{/lang}</label>
					
					<select id="contentBodyDisplay_{$blockID}" name="contentBodyDisplay" data-block-id="{$blockID}" data-is-block="true">
						<option value="default"{if $contentBodyDisplaySelected == 'default'} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.display.contentBodyDisplay.default{/lang}</option>
						<option value="full"{if $contentBodyDisplaySelected == 'full'} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.display.contentBodyDisplay.full{/lang}</option>
						<option value="excerpt"{if $contentBodyDisplaySelected == 'excerpt'} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.display.contentBodyDisplay.excerpt{/lang}</option>
						<option value="hide"{if $contentBodyDisplaySelected == 'hide'} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.display.contentBodyDisplay.hide{/lang}</option>
					</select>
				</dd>
				<dd class="inputCheckbox">
					<label for="showContent_{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.display.showContent.description{/lang}">{lang}ultimate.visualEditor.contentTab.display.showContent{/lang}</label>
					<input type="checkbox" id="showContent_{$blockID}" name="showContent" value="{$showContent}" data-block-id="{$blockID}" data-is-block="true" />
				</dd>
				<dd class="inputSelect">
					<label for="commentsVisibility_{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.display.commentsVisibility.description{/lang}">{lang}ultimate.visualEditor.contentTab.display.commentsVisibility{/lang}</label>
					
					<select id="commentsVisibility_{$blockID}" name="commentsVisibility" data-block-id="{$blockID}" data-is-block="true">
						<option value="auto"{if $commentsVisibilitySelected == 'auto'} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.display.commentsVisibility.auto{/lang}</option>
						<option value="hide"{if $commentsVisibilitySelected == 'hide'} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.display.commentsVisibility.hide{/lang}</option>
						<option value="show"{if $commentsVisibilitySelected == 'show'} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.display.commentsVisibility.show{/lang}</option>
					</select>
				</dd>
				<dd class="inputInteger">
					<label for="featuredContents_{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.display.featuredContents.description{/lang}">{lang}ultimate.visualEditor.contentTab.display.featuredContents{/lang}</label>
					<input type="number" min="0" max="100" step="1" id="featuredContents_{$blockID}" name="featuredContents" value="{$featuredContents}" data-block-id="{$blockID}" data-is-block="true" />
				</dd>
				<dd class="inputCheckbox">
					<label for="showInlineEdit_{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.display.showInlineEdit.description{/lang}">{lang}ultimate.visualEditor.contentTab.display.showInlineEdit{/lang}</label>
					<input type="checkbox" id="showInlineEdit_{$blockID}" name="showInlineEdit" value="{$showInlineEdit}" data-block-id="{$blockID}" data-is-block="true" />
				</dd>
			</dl>
		</div>
		<div id="block_{$blockID}_tab_meta" class="container tabMenuContent containerPadding" data-parent-menu-item="block_{$blockID}_tab_meta">
			<div class="info">
				<p>{lang}ultimate.visualEditor.contentTab.meta.info{/lang}</p>
			</div>
			<dl class="wide">
				<dd class="inputMultiSelect">
					<label for="contentMetaDisplay_{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.meta.contentMetaDisplay.description{/lang}">{lang}ultimate.visualEditor.contentTab.meta.contentMetaDisplay{/lang}</label>
					
					<select id="contentMetaDisplay_{$blockID}" name="contentMetaDisplay[]" data-block-id="{$blockID}" data-is-block="true" multiple="multiple">
						<option value="category"{if $contentMetaDisplaySelected['category']|isset} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.meta.contentMetaDisplay.category{/lang}</option>
						<option value="page"{if $contentMetaDisplaySelected['page']|isset} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.meta.contentMetaDisplay.page{/lang}</option>
						<option value="content"{if $contentMetaDisplaySelected['content']|isset} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.meta.contentMetaDisplay.content{/lang}</option>
					</select>
				</dd>
				<dd class="inputTextarea">
					<label for="metaAboveContent_{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.meta.metaAboveContent.description{/lang}">{lang}ultimate.visualEditor.contentTab.meta.metaAboveContent{/lang}</label>
					<textarea id="metaAboveContent_{$blockID}" name="metaAboveContent" cols="40" rows="10" data-block-id="{$blockID}" data-is-block="true">{@$i18nPlainValues[$metaAboveContentID]}</textarea>
				</dd>
				<dd class="inputTextarea">
					<label for="metaBelowContent_{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.meta.metaBelowContent.description{/lang}">{lang}ultimate.visualEditor.contentTab.meta.metaBelowContent{/lang}</label>
					<textarea id="metaBelowContent_{$blockID}" name="metaBelowContent" cols="40" rows="10" data-block-id="{$blockID}" data-is-block="true">{@$i18nPlainValues[$metaBelowContentID]}</textarea>
				</dd>
			</dl>
		</div>
	<div class="formSubmit">
		<input type="submit" id="blockSubmitButton" name="submitButton" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
	</div>
	</form>
</div>