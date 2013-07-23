{capture assign='pageTitle'}{lang}wcf.acp.ultimate.template.list{/lang}{/capture}
{include file='header' application='ultimate'}

<script type="text/javascript">
	//<![CDATA[
	$(function() {
		var actionObjects = { };
		actionObjects['de.plugins-zum-selberbauen.ultimate.template'] = { };
		actionObjects['de.plugins-zum-selberbauen.ultimate.template']['delete'] = new WCF.Action.Delete('ultimate\\data\\template\\TemplateAction', $('.jsTemplateRow'), $('#templateTableContainer .menu li:first-child .badge'));
		
		WCF.Clipboard.init('ultimate\\acp\\page\\UltimateTemplateListPage', {@$hasMarkedItems}, actionObjects);
		
		var options = { };
		options.emptyMessage = '{lang}wcf.acp.ultimate.template.noContents{/lang}';
		{if $pages > 1}
			options.refreshPage = true;
		{/if}
		
		new WCF.Table.EmptyTableHandler($('#templateTableContainer'), 'jsTemplateRow', options);
	});
	//]]>
</script>

<header class="boxHeadline">
	<h1>{lang}wcf.acp.ultimate.template.list{/lang}</h1>
</header>

{assign var=encodedURL value=$url|rawurlencode}
{assign var=encodedAction value=$action|rawurlencode}
<div class="contentNavigation">
	{pages print=true assign=pagesLinks application='ultimate' controller="UltimateTemplateList" link="pageNo=%d&action=$encodedAction&sortField=$sortField&sortOrder=$sortOrder"}
</div>

{hascontent}
<div id="templateTableContainer" class="tabularBox tabularBoxTitle marginTop">
	<header>
		<h2>{lang}wcf.acp.ultimate.template.list{/lang} <span class="badge badgeInverse" title="{lang}wcf.acp.ultimate.template.list.count{/lang}">{#$items}</span></h2>
	</header>
	<table class="table jsClipboardContainer" data-type="de.plugins-zum-selberbauen.ultimate.template">
		<thead>
			<tr>
				<th class="columnMark"><label><input type="checkbox" class="jsClipboardMarkAll" /></label></th>
				<th class="columnID{if $sortField == 'templateID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link application='ultimate' controller='UltimateTemplateList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=templateID&sortOrder={if $sortField == 'templateID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
				<th class="columnTitle{if $sortField == 'templateName'} active {@$sortOrder}{/if}"><a href="{link application='ultimate' controller='UltimateTemplateList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=templateName&sortOrder={if $sortField == 'templateName' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.template.name{/lang}</a></th>
				{event name='headColumns'}
			</tr>
		</thead>
		
		<tbody>
			{content}
				{foreach from=$objects item=template}
					<tr id="templateContainer{@$template->templateID}" class="jsTemplateRow">
						<td class="columnMark"><input type="checkbox" class="jsClipboardItem" data-object-id="{@$template->templateID}" /></td>
						<td class="columnIcon">
							
						{if $__wcf->session->getPermission('admin.content.ultimate.canManageTemplates')}
								<a href="{link application='ultimate' controller='UltimateTemplateEdit' id=$template->templateID}{/link}"><span title="{lang}wcf.acp.ultimate.template.edit{/lang}" class="icon icon16 icon-pencil jsTooltip"></span></a>
							{else}
								<span title="{lang}wcf.acp.ultimate.template.edit{/lang}" class="icon icon16 icon-pencil disabled"></span>
							{/if}
							
							{if $__wcf->session->getPermission('admin.content.ultimate.canManageTemplates')}
								<span title="{lang}wcf.acp.ultimate.template.delete{/lang}" class="icon icon16 icon-remove jsTooltip jsDeleteButton" data-object-id="{@$template->templateID}" data-confirm-message="{lang}wcf.acp.ultimate.template.delete.sure{/lang}"></span>
							{else}
								<span title="{lang}wcf.acp.ultimate.template.delete{/lang}" class="icon icon16 icon-remove disabled"></span>
							{/if}
							
							{event name='buttons'}
						</td>
						<td class="columnID"><p>{@$template->templateID}</p></td>
						<td class="columnTitle"><p>{if $__wcf->session->getPermission('admin.content.ultimate.canManageTemplates')}<a title="{lang}wcf.acp.ultimate.template.edit{/lang}" href="{link application='ultimate' controller='UltimateTemplateEdit' id=$template->templateID}{/link}">{lang}{@$template->templateName}{/lang}</a>{else}{lang}{@$template->templateName}{/lang}{/if}</p></td>
						
						{event name='columns'}
					</tr>				
				{/foreach}
			{/content}
		</tbody>
	</table>
</div>
{hascontentelse}
<p class="info">{lang}wcf.acp.ultimate.template.noContents{/lang}</p>
{/hascontent}

<div class="contentNavigation">
	{@$pagesLinks}
		
	<div class="clipboardEditor jsClipboardEditor" data-types="[ 'de.plugins-zum-selberbauen.ultimate.template' ]"></div>
</div>
</div>

{include file='footer'}
