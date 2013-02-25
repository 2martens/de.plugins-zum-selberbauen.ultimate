<div id="block-{$blockID}-tab" class="tabMenuContainer tabMenuContent containerPadding panel" data-store="block-{$blockID}-tab-mode">
	<form method="post" action="">
	<nav class="tabMenu subTabsContainer">
		<ul class="subTabs">
			<li><a href="#block-{$blockID}-tab-mode" title="{lang}ultimate.visualEditor.contentTab.mode{/lang}">{lang}ultimate.visualEditor.contentTab.mode{/lang}</a></li>
			<li><a href="#block-{$blockID}-tab-query" title="{lang}ultimate.visualEditor.contentTab.query{/lang}"{if $queryModeSelected == 'default'} class="ultimateHidden"{/if}>{lang}ultimate.visualEditor.contentTab.query{/lang}</a></li>
			<li><a href="#block-{$blockID}-tab-display" title="{lang}ultimate.visualEditor.contentTab.display{/lang}">{lang}ultimate.visualEditor.contentTab.display{/lang}</a></li>
			<li><a href="#block-{$blockID}-tab-meta" title="{lang}ultimate.visualEditor.contentTab.meta{/lang}">{lang}ultimate.visualEditor.contentTab.meta{/lang}</a></li>
		</ul>
	</nav>
	
	<div class="subTabsContentContainer">
		<div id="block-{$blockID}-tab-mode" class="tabMenuContent subTabsContent containerPadding">
			<div class="info">
				<p>{lang}ultimate.visualEditor.contentTab.mode.info{/lang}</p>
			</div>
			<dl class="wide">
				<dd class="inputSelect">
					<label for="queryMode-{$blockID}">{lang}ultimate.visualEditor.contentTab.mode.queryMode{/lang}</label>
					
					<select id="queryMode-{$blockID}" name="queryMode" data-block-id="{$blockID}" data-is-block="true">
						<option value="default"{if $queryModeSelected == 'default'} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.mode.queryMode.default{/lang}</option>
						<option value="custom"{if $queryModeSelected == 'custom'} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.mode.queryMode.custom{/lang}</option>
					</select>
				</dd>
			</dl>
		</div>
		<div id="block-{$blockID}-tab-query" class="tabMenuContent subTabsContent containerPadding">
			<div class="info">
				<p>{lang}ultimate.visualEditor.contentTab.query.info{/lang}</p>
			</div>
			<dl class="wide">
				<dd class="inputSelect">
					<label for="fetchPageContent-{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.query.fetchPageContent.description{/lang}">{lang}ultimate.visualEditor.contentTab.query.fetchPageContent{/lang}</label>
					
					<select id="fetchPageContent-{$blockID}" name="fetchPageContent" data-block-id="{$blockID}" data-is-block="true">
						<option value="none"{if $fetchPageContentSelected == 'none'} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.query.fetchPageContent.none{/lang}</option>
						{foreach from=$pages key=pageID item=page}
						<option value="{$pageID}"{if $fetchPageContentSelected == $pageID} selected="selected"{/if}>{$page}</option>
						{/foreach}
					</select>
				</dd>
				<dd class="inputMultiSelect">
					<label for="categories-{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.query.categories.description{/lang}">{lang}ultimate.visualEditor.contentTab.query.categories{/lang}</label>
					
					<select id="categories-{$blockID}" name="categories" data-block-id="{$blockID}" data-is-block="true" multiple="multiple">
						{foreach from=$categories key=categoryID item=category}
						<option value="{$categoryID}"{if $categoriesSelected[$categoryID]|isset} selected="selected"{/if}>{@$category}</option>
						{/foreach}
					</select>
				</dd>
				<dd class="inputSelect">
					<label for="categoryMode-{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.query.categoryMode.description{/lang}">{lang}ultimate.visualEditor.contentTab.query.categoryMode{/lang}</label>
					
					<select id="categoryMode-{$blockID}" name="categoryMode" data-block-id="{$blockID}" data-is-block="true">
						<option value="include"{if $categoryModeSelected == 'include'} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.query.categoryMode.include{/lang}</option>
						<option value="exclude"{if $categoryModeSelected == 'exclude'} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.query.categoryMode.exclude{/lang}</option>
					</select>
				</dd>
				<dd class="inputMultiSelect">
					<label for="authors-{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.query.authors.description{/lang}">{lang}ultimate.visualEditor.contentTab.query.authors{/lang}</label>
					
					<select id="authors-{$blockID}" name="authors" data-block-id="{$blockID}" data-is-block="true" multiple="multiple">
						{foreach from=$authors key=authorID item=author}
						<option value="{$authorID}"{if $authorsSelected[$authorID]|isset} selected="selected"{/if}>{@$author->getTitle()}</option>
						{/foreach}
					</select>
				</dd>
				<dd class="inputInteger">
					<label for="numberOfContents-{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.query.numberOfContents.description{/lang}">{lang}ultimate.visualEditor.contentTab.query.numberOfContents{/lang}</label>
					<input type="number" step="1" min="1" max="{$contents|count}" id="numberOfPosts-{$blockID}" name="numberOfContents" value="{$numberOfContents}" data-block-id="{$blockID}" data-is-block="true" />
				</dd>
				<dd class="inputInteger">
					<label for="offset-{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.query.offset.description{/lang}">{lang}ultimate.visualEditor.contentTab.query.offset{/lang}</label>
					<input type="number" step="1" min="0" max="{$contents|count - 1}" id="offset-{$blockID}" name="offset" value="{$offset}" data-block-id="{$blockID}" data-is-block="true" />
				</dd>
				<dd class="inputSelect">
					<label for="sortField-{$blockID}">{lang}ultimate.visualEditor.contentTab.query.sortField{/lang}</label>
					
					<select id="sortField-{$blockID}" name="sortField" data-block-id="{$blockID}" data-is-block="true">
						<option value="contentID"{if $sortFieldSelected == 'contentID'} selected="selected"{/if}>{lang}wcf.global.objectID{/lang}</option>
						<option value="contentTitle"{if $sortFieldSelected == 'contentTitle'} selected="selected"{/if}>{lang}wcf.acp.ultimate.content.title{/lang}</option>
						<option value="contentAuthor"{if $sortFieldSelected == 'contentAuthor'} selected="selected"{/if}>{lang}wcf.acp.ultimate.author{/lang}</option>
						<option value="lastModified"{if $sortFieldSelected == 'lastModified'} selected="selected"{/if}>{lang}wcf.acp.ultimate.lastModified{/lang}</option>
					</select>
				</dd>
				<dd class="inputSelect">
					<label for="sortOrder-{$blockID}">{lang}ultimate.visualEditor.contentTab.query.sortOrder{/lang}</label>
					
					<select id="sortOrder-{$blockID}" name="sortOrder" data-block-id="{$blockID}" data-is-block="true">
						<option value="ASC"{if $sortOrderSelected == 'ASC'} selected="selected"{/if}>{lang}wcf.global.sortOrder.ascending{/lang}</option>
						<option value="DESC"{if $sortOrderSelected == 'DESC'} selected="selected"{/if}>{lang}wcf.global.sortOrder.descending{/lang}</option>
					</select>
				</dd>
			</dl>
		</div>
		<div id="block-{$blockID}-tab-display" class="tabMenuContent subTabsContent containerPadding">
			<dl class="wide">
				<dd class="inputText">
					<label for="readMoreText-{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.display.readMoreText.description{/lang}">{lang}ultimate.visualEditor.contentTab.display.readMoreText{/lang}</label>
					{include file='multipleLanguageInput' elementIdentifier='readMoreText'}
					<input type="text" id="readMoreText-{$blockID}" name="readMoreText" value="{@$i18nPlainValues['readMoreText']}" data-block-id="{$blockID}" data-is-block="true" />
				</dd>
				<dd class="inputCheckbox">
					<label for="showTitles-{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.display.showTitles.description{/lang}">{lang}ultimate.visualEditor.contentTab.display.showTitles{/lang}</label>
					<input type="checkbox" id="showTitles-{$blockID}" name="showTitles" value="{$showTitles}" data-block-id="{$blockID}" data-is-block="true" />
				</dd>
				<dd class="inputSelect">
					<label for="contentBodyDisplay-{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.display.contentBodyDisplay.description{/lang}">{lang}ultimate.visualEditor.contentTab.display.contentBodyDisplay{/lang}</label>
					
					<select id="contentBodyDisplay-{$blockID}" name="contentBodyDisplay" data-block-id="{$blockID}" data-is-block="true">
						<option value="default"{if $contentBodyDisplaySelected == 'default'} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.display.contentBodyDisplay.default{/lang}</option>
						<option value="full"{if $contentBodyDisplaySelected == 'full'} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.display.contentBodyDisplay.full{/lang}</option>
						<option value="excerpt"{if $contentBodyDisplaySelected == 'excerpt'} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.display.contentBodyDisplay.excerpt{/lang}</option>
						<option value="hide"{if $contentBodyDisplaySelected == 'hide'} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.display.contentBodyDisplay.hide{/lang}</option>
					</select>
				</dd>
				<dd class="inputCheckbox">
					<label for="showContent-{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.display.showContent.description{/lang}">{lang}ultimate.visualEditor.contentTab.display.showContent{/lang}</label>
					<input type="checkbox" id="showContent-{$blockID}" name="showContent" value="{$showContentBody}" data-block-id="{$blockID}" data-is-block="true" />
				</dd>
				<dd class="inputSelect">
					<label for="commentsVisibility-{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.display.commentsVisibility.description{/lang}">{lang}ultimate.visualEditor.contentTab.display.commentsVisibility{/lang}</label>
					
					<select id="commentsVisibility-{$blockID}" name="commentsVisibility" data-block-id="{$blockID}" data-is-block="true">
						<option value="auto"{if $commentsVisibilitySelected == 'auto'} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.display.commentsVisibility.auto{/lang}</option>
						<option value="hide"{if $commentsVisibilitySelected == 'hide'} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.display.commentsVisibility.hide{/lang}</option>
						<option value="show"{if $commentsVisibilitySelected == 'show'} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.display.commentsVisibility.show{/lang}</option>
					</select>
				</dd>
				<dd class="inputInteger">
					<label for="featuredContents-{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.display.featuredContents.description{/lang}">{lang}ultimate.visualEditor.contentTab.display.featuredContents{/lang}</label>
					<input type="number" min="0" max="100" step="1" id="featuredContents-{$blockID}" name="featuredContents" value="{$featuredContents}" data-block-id="{$blockID}" data-is-block="true" />
				</dd>
				<dd class="inputCheckbox">
					<label for="showInlineEdit-{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.display.showInlineEdit.description{/lang}">{lang}ultimate.visualEditor.contentTab.display.showInlineEdit{/lang}</label>
					<input type="checkbox" id="showInlineEdit-{$blockID}" name="showInlineEdit" value="{$showInlineEdit}" data-block-id="{$blockID}" data-is-block="true" />
				</dd>
			</dl>
		</div>
		<div id="block-{$blockID}-tab-meta" class="tabMenuContent subTabsContent containerPadding">
			<div class="info">
				<p>{lang}ultimate.visualEditor.contentTab.meta.info{/lang}</p>
			</div>
			<dl class="wide">
				<dd class="inputMultiSelect">
					<label for="contentMetaDisplay-{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.meta.contentMetaDisplay.description{/lang}">{lang}ultimate.visualEditor.contentTab.meta.contentMetaDisplay{/lang}</label>
					
					<select id="contentMetaDisplay-{$blockID}" name="contentMetaDisplay" data-block-id="{$blockID}" data-is-block="true" multiple="multiple">
						<option value="category"{if $contentMetaDisplaySelected['category']|isset} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.meta.contentMetaDisplay.category{/lang}</option>
						<option value="page"{if $contentMetaDisplaySelected['page']|isset} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.meta.contentMetaDisplay.page{/lang}</option>
						<option value="content"{if $contentMetaDisplaySelected['content']|isset} selected="selected"{/if}>{lang}ultimate.visualEditor.contentTab.meta.contentMetaDisplay.content{/lang}</option>
					</select>
				</dd>
				<dd class="inputTextarea">
					<label for="metaAboveContent-{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.meta.metaAboveContent.description{/lang}">{lang}ultimate.visualEditor.contentTab.meta.metaAboveContent{/lang}</label>
					{include file='multipleLanguageInput' elementIdentifier='metaAboveContent'}
					<textarea id="metaAboveContent-{$blockID}" name="metaAboveContent" cols="40" rows="10" data-block-id="{$blockID}" data-is-block="true">{@$i18nPlainValues['metaAboveContent']}</textarea>
				</dd>
				<dd class="inputTextarea">
					<label for="metaBelowContent-{$blockID}" class="jsTooltip" title="{lang}ultimate.visualEditor.contentTab.meta.metaBelowContent.description{/lang}">{lang}ultimate.visualEditor.contentTab.meta.metaBelowContent{/lang}</label>
					{include file='multipleLanguageInput' elementIdentifier='metaBelowContent'}
					<textarea id="metaBelowContent-{$blockID}" name="metaBelowContent" cols="40" rows="10" data-block-id="{$blockID}" data-is-block="true">{@$i18nPlainValues['metaBelowContent']}</textarea>
				</dd>
			</dl>
		</div>
	</div>
	<div class="formSubmit">
		<input type="submit" id="blockSubmitButton" name="submitButton" value="{lang}wcf.global.submit{/lang}" accesskey="s" />
	</div>
	</form>
	<script type="text/javascript">
	/* <![CDATA[ */
	$(function() {
		$('#queryMode-{$blockID}').change(function(event) {
			$('ul.subTabs > li > a[href="#block-{$blockID}-tab-query"]').toggleClass('ultimateHidden');
		});
	});
	/* ]]> */
	</script>
</div>