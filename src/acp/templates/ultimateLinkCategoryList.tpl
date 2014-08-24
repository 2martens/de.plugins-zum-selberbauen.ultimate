{capture assign='pageTitle'}{@$objectType->getProcessor()->getLanguageVariable('list')}{/capture}
{include file='header' application='ultimate'}

{if $categoryNodeList|count}
	<script data-relocate="true "type="text/javascript">
		//<![CDATA[
		$(function() {
			{if $collapsibleObjectTypeID && $categoryNodeList|count > 1}
				new WCF.ACP.Category.Collapsible('wcf\\data\\category\\CategoryAction', {@$collapsibleObjectTypeID});
			{/if}
			
			{if $objectType->getProcessor()->canDeleteCategory()}
				new WCF.ACP.Category.Delete('wcf\\data\\category\\CategoryAction', $('.jsCategory'));
			{/if}
			{if $objectType->getProcessor()->canEditCategory()}
				new WCF.Action.Toggle('wcf\\data\\category\\CategoryAction', $('.jsCategory'), '> .buttons > .jsToggleButton');
				
				{if $categoryNodeList|count > 1}
					var sortableNodes = $('.sortableNode');
					sortableNodes.each(function(index, node) {
						$(node).wcfIdentify();
					});
					
					new WCF.Sortable.List('categoryList', 'wcf\\data\\category\\CategoryAction', 0{if $objectType->getProcessor()->getMaximumNestingLevel() != -1}, {
						/**
						 * Updates the sortable nodes after a sorting is started with
						 * regard to their possibility to have child the currently sorted
						 * category as a child category.
						 */
						start: function(event, ui) {
							var sortedListItem = $(ui.item);
							var itemNestingLevel = sortedListItem.find('.sortableList:has(.sortableNode)').length;
							
							sortableNodes.each(function(index, node) {
								node = $(node);
								
								if (node.attr('id') != sortedListItem.attr('id')) {
									if (node.parents('.sortableList').length + itemNestingLevel >= {@$objectType->getProcessor()->getMaximumNestingLevel() + 1}) {
										node.addClass('sortableNoNesting');
									}
									else if (node.hasClass('sortableNoNesting')) {
										node.removeClass('sortableNoNesting');
									}
								}
							});
						},
						/**
						 * Updates the sortable nodes after a sorting is completed with
						 * regard to their possibility to have child categories.
						 */
						stop: function(event, ui) {
							sortableNodes.each(function(index, node) {
								node = $(node);
								
								if (node.parents('.sortableList').length == {@$objectType->getProcessor()->getMaximumNestingLevel() + 1}) {
									node.addClass('sortableNoNesting');
								}
								else if (node.hasClass('sortableNoNesting')) {
									node.removeClass('sortableNoNesting');
								}
							});
						}
					}{/if});
				{/if}
			{/if}
		});
		//]]>
	</script>
{/if}

<header class="box48 boxHeadline">
	<h1>{@$objectType->getProcessor()->getLanguageVariable('list')}</h1>
</header>

{hascontent}
	<div class="contentNavigation">
		<nav>
			<ul>
				{content}
					{if $objectType->getProcessor()->canAddCategory()}
						<li><a href="{link application='ultimate' controller=$addController}{/link}" title="{$objectType->getProcessor()->getLanguageVariable('add')}" class="button"><span class="icon icon16 icon-plus"></span> <span>{@$objectType->getProcessor()->getLanguageVariable('add')}</span></a></li>
					{/if}
					
					{event name='contentNavigationButtons'}
				{/content}
			</ul>
		</nav>
	</div>
{/hascontent}

{if $categoryNodeList|count}
	<section id="categoryList" class="container containerPadding marginTop shadow{if $objectType->getProcessor()->canEditCategory() && $categoryNodeList|count > 1} sortableListContainer{/if}">
		<ol class="categoryList sortableList" data-object-id="0">
			{assign var=oldDepth value=0}
			{foreach from=$categoryNodeList item=category}
				{section name=i loop=$oldDepth-$categoryNodeList->getDepth()}</ol></li>{/section}
				
				<li class="{if $objectType->getProcessor()->canEditCategory() && $categoryNodeList|count > 1}sortableNode {if $categoryNodeList->getDepth() == $objectType->getProcessor()->getMaximumNestingLevel()}sortableNoNesting {/if}{/if}jsCategory" data-object-id="{@$category->categoryID}"{if $collapsedCategoryIDs|is_array} data-is-open="{if $collapsedCategoryIDs[$category->categoryID]|isset}0{else}1{/if}"{/if}>
					<span class="sortableNodeLabel">
						<span class="buttons">
							{if $objectType->getProcessor()->canEditCategory()}
								<a href="{link application='ultimate' controller=$editController id=$category->categoryID title=$category->getTitle()}{/link}"><span title="{lang}wcf.global.button.edit{/lang}" class="icon icon16 icon-pencil jsTooltip"></span></a>
							{else}
								<span title="{lang}wcf.global.button.edit{/lang}" class="icon icon16 icon-pencil disabled"></span>
							{/if}

							{if $objectType->getProcessor()->canDeleteCategory() && $category->categoryID != $defaultLinkCategoryID}
								<span title="{lang}wcf.global.button.delete{/lang}" class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" data-object-id="{@$category->categoryID}" data-confirm-message="{@$objectType->getProcessor()->getLanguageVariable('delete.sure')}"></span>
							{else}
								<span title="{lang}wcf.global.button.delete{/lang}" class="icon icon16 icon-remove disabled"></span>
							{/if}

							{if $objectType->getProcessor()->canEditCategory()}
								<span title="{lang}wcf.global.button.{if !$category->isDisabled}disable{else}enable{/if}{/lang}" class="icon icon16 icon-{if !$category->isDisabled}circle-blank{else}off{/if} jsToggleButton jsTooltip pointer" data-object-id="{@$category->categoryID}"></span>
							{else}
								<span title="{lang}wcf.global.button.{if !$category->isDisabled}enable{else}disable{/if}{/lang}" class="icon icon16 icon-{if !$category->isDisabled}circle-blank{else}off{/if} disabled"></span>
							{/if}

							{event name='buttons'}
						</span>

						<span class="title">
							{$category->getTitle()}
						</span>
					</span>
					
					<ol class="categoryList sortableList" data-object-id="{@$category->categoryID}">
				{if !$categoryNodeList->current()->hasChildren()}
					</ol></li>
				{/if}
				{assign var=oldDepth value=$categoryNodeList->getDepth()}
			{/foreach}
			{section name=i loop=$oldDepth}</ol></li>{/section}
		</ol>
		
		{if $objectType->getProcessor()->canEditCategory() && $categoryNodeList|count > 1}
			<div class="formSubmit">
				<button class="button default" data-type="submit">{lang}wcf.global.button.save{/lang}</button>
			</div>
		{/if}
	</section>
		
	{hascontent}
		<div class="contentNavigation">
			<nav>
				<ul>
					{content}
						{if $objectType->getProcessor()->canAddCategory()}
							<li><a href="{link application='ultimate' controller=$addController}{/link}" title="{$objectType->getProcessor()->getLanguageVariable('add')}" class="button"><span class="icon icon16 icon-plus"></span> <span>{@$objectType->getProcessor()->getLanguageVariable('add')}</span></a></li>
						{/if}

						{event name='contentNavigationButtons'}
					{/content}
				</ul>
			</nav>
		</div>
	{/hascontent}
{else}
	<p class="info">{@$objectType->getProcessor()->getLanguageVariable('noneAvailable')}</p>
{/if}

{include file='footer'}
