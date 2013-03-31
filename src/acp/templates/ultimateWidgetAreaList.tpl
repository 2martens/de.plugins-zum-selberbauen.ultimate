{include file='header'}

<script type="text/javascript">
	//<![CDATA[
	$(function() {
		var actionObjects = { };
		actionObjects['de.plugins-zum-selberbauen.ultimate.widgetArea'] = { };
		actionObjects['de.plugins-zum-selberbauen.ultimate.widgetArea']['delete'] = new WCF.Action.Delete('ultimate\\data\\widget\\area\\WidgetAreaAction', $('.jsWidgetAreaRow'), $('#widgetAreaTableContainer .menu li:first-child .badge'));
		
		WCF.Clipboard.init('ultimate\\acp\\page\\UltimateWidgetAreaListPage', {@$hasMarkedItems}, actionObjects);
		
		var options = { };
		options.emptyMessage = '{lang}wcf.acp.ultimate.widgetArea.noContents{/lang}';
		{if $pages > 1}
			options.refreshPage = true;
		{/if}
		
		new WCF.Table.EmptyTableHandler($('#widgetAreaTableContainer'), 'jsWidgetAreaRow', options);
	});
	//]]>
</script>

<header class="boxHeadline">
	<hgroup>
		<h1>{lang}wcf.acp.ultimate.widgetArea.list{/lang}</h1>
	</hgroup>
</header>

{assign var=encodedURL value=$url|rawurlencode}
{assign var=encodedAction value=$action|rawurlencode}
<div class="contentNavigation">
	{pages print=true assign=pagesLinks controller="UltimateWidgetAreaList" link="pageNo=%d&action=$encodedAction&sortField=$sortField&sortOrder=$sortOrder"}
</div>

{hascontent}
<div id="widgetAreaTableContainer" class="tabularBox tabularBoxTitle marginTop shadow">
	<nav class="menu tableMenu">
		<ul>
			<li{if $action == ''} class="active"{/if}>
				<a href="{link controller='UltimateWidgetAreaList'}{/link}"><span>{lang}wcf.acp.ultimate.widgetArea.list.all{/lang}</span> <span class="badge badgeInverse" title="{lang}wcf.acp.ultimate.widgetArea.list.count{/lang}">{#$items}</span></a>
			</li>
			
			{event name='ultimateWidgetAreaListOptions'}
		</ul>
	</nav>
	<table class="table jsClipboardContainer" data-type="de.plugins-zum-selberbauen.ultimate.widgetArea">
		<thead>
			<tr>
				<th class="columnMark"><label><input type="checkbox" class="jsClipboardMarkAll" /></label></th>
				<th class="columnID{if $sortField == 'widgetAreaID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link controller='UltimateWidgetAreaList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=widgetAreaID&sortOrder={if $sortField == 'widgetAreaID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
				<th class="columnTitle{if $sortField == 'widgetAreaName'} active {@$sortOrder}{/if}"><a href="{link controller='UltimateWidgetAreaList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=widgetAreaName&sortOrder={if $sortField == 'widgetAreaName' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.widgetArea.name{/lang}</a></th>
				{event name='headColumns'}
			</tr>
		</thead>
		
		<tbody>
			{content}
				{foreach from=$objects item=widgetArea}
					<tr id="widgetAreaContainer{@$widgetArea->widgetAreaID}" class="jsWidgetAreaRow">
						<td class="columnMark"><input type="checkbox" class="jsClipboardItem" data-object-id="{@$widgetArea->widgetAreaID}" /></td>
						<td class="columnIcon">
							
						{if $__wcf->session->getPermission('admin.content.ultimate.canEditWidgetArea')}
								<a href="{link controller='UltimateWidgetAreaEdit' id=$widgetArea->widgetAreaID}{/link}"><span title="{lang}wcf.acp.ultimate.widgetArea.edit{/lang}" class="icon icon16 icon-pencil jsTooltip"></span></a>
							{else}
								<span title="{lang}wcf.acp.ultimate.widgetArea.edit{/lang}" class="icon icon16 icon-pencil disabled"></span>
							{/if}
							
							{if $__wcf->session->getPermission('admin.content.ultimate.canDeleteWidgetArea')}
								<span title="{lang}wcf.acp.ultimate.widgetArea.delete{/lang}" class="icon icon16 icon-remove jsTooltip jsDeleteButton" data-object-id="{@$widgetArea->widgetAreaID}" data-confirm-message="{lang}wcf.acp.ultimate.widgetArea.delete.sure{/lang}"></span>
							{else}
								<span title="{lang}wcf.acp.ultimate.widgetArea.delete{/lang}" class="icon icon16 icon-remove disabled"></span>
							{/if}
							
							{event name='buttons'}
						</td>
						<td class="columnID"><p>{@$widgetArea->widgetAreaID}</p></td>
						<td class="columnTitle"><p>{if $__wcf->session->getPermission('admin.content.ultimate.canEditWidgetArea')}<a title="{lang}wcf.acp.ultimate.widgetArea.edit{/lang}" href="{link controller='UltimateWidgetAreaEdit' id=$widgetArea->widgetAreaID}{/link}">{lang}{@$widgetArea->widgetAreaName}{/lang}</a>{else}{lang}{@$widgetArea->widgetAreaName}{/lang}{/if}</p></td>
						
						{event name='columns'}
					</tr>				
				{/foreach}
			{/content}
		</tbody>
	</table>
</div>
{hascontentelse}
<p class="info">{lang}wcf.acp.ultimate.widgetArea.noContents{/lang}</p>
{/hascontent}

<div class="contentNavigation">
	{@$pagesLinks}
		
	<div class="clipboardEditor jsClipboardEditor" data-types="[ 'de.plugins-zum-selberbauen.ultimate.widgetArea' ]"></div>
</div>
</div>

{include file='footer'}