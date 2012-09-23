{include file='header'}

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
	<hgroup>
		<h1>{lang}wcf.acp.ultimate.template.list{/lang}</h1>
	</hgroup>
</header>

{assign var=encodedURL value=$url|rawurlencode}
{assign var=encodedAction value=$action|rawurlencode}
<div class="contentNavigation">
	{pages print=true assign=pagesLinks controller="UltimateTemplateList" link="pageNo=%d&action=$encodedAction&sortField=$sortField&sortOrder=$sortOrder"}
</div>

{hascontent}
<div id="templateTableContainer" class="tabularBox marginTop shadow">
	<nav class="menu">
		<ul>
			<li{if $action == ''} class="active"{/if}><a href="{link controller='UltimateTemplateList'}{/link}"><span>{lang}wcf.acp.ultimate.template.list.all{/lang}</span> <span class="badge" title="{lang}wcf.acp.ultimate.template.list.count{/lang}">{#$items}</span></a></li>
			
			{event name='ultimateTemplateListOptions'}
		</ul>
	</nav>
	<table class="table jsClipboardContainer" data-type="de.plugins-zum-selberbauen.ultimate.template">
		<thead>
			<tr>
				<th class="columnMark"><label><input type="checkbox" class="jsClipboardMarkAll" /></label></th>
				<th class="columnID{if $sortField == 'templateID'} active{/if}" colspan="2"><a href="{link controller='UltimateTemplateList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=templateID&sortOrder={if $sortField == 'templateID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}{if $sortField == 'templateID'} <img src="{@$__wcf->getPath()}icon/sort{@$sortOrder}.svg" alt="{if $sortOrder == 'ASC'}{lang}wcf.global.sortOrder.ascending{/lang}{else}{lang}wcf.global.sortOrder.descending{/lang}{/if}" />{/if}</a></th>
				<th class="columnTitle{if $sortField == 'templateName'} active{/if}"><a href="{link controller='UltimateTemplateList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=templateName&sortOrder={if $sortField == 'templateName' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.template.name{/lang}{if $sortField == 'templateName'} <img src="{@$__wcf->getPath()}icon/sort{@$sortOrder}.svg" alt="{if $sortOrder == 'ASC'}{lang}wcf.global.sortOrder.ascending{/lang}{else}{lang}wcf.global.sortOrder.descending{/lang}{/if}" />{/if}</a></th>
				{event name='headColumns'}
			</tr>
		</thead>
		
		<tbody>
			{content}
				{foreach from=$objects item=template}
					<tr id="templateContainer{@$template->templateID}" class="jsTemplateRow">
						<td class="columnMark"><input type="checkbox" class="jsClipboardItem" data-object-id="{@$template->templateID}" /></td>
						<td class="columnIcon">
							
						{if $__wcf->session->getPermission('admin.content.ultimate.canEditTemplate')}
								<a href="{link controller='UltimateTemplateEdit' id=$template->templateID}{/link}"><img src="{@$__wcf->getPath()}icon/edit.svg" alt="" title="{lang}wcf.acp.ultimate.template.edit{/lang}" class="icon16 jsTooltip" /></a>
							{else}
								<img src="{@$__wcf->getPath()}icon/edit.svg" alt="" title="{lang}wcf.acp.ultimate.template.edit{/lang}" class="icon16 disabled" />
							{/if}
							
							{if $__wcf->session->getPermission('admin.content.ultimate.canDeleteTemplate')}
								<img src="{@$__wcf->getPath()}icon/delete.svg" alt="" title="{lang}wcf.acp.ultimate.template.delete{/lang}" class="icon16 jsTooltip jsDeleteButton" data-object-id="{@$template->templateID}" data-confirm-message="{lang}wcf.acp.ultimate.template.delete.sure{/lang}" />
							{else}
								<img src="{@$__wcf->getPath()}icon/delete.svg" alt="" title="{lang}wcf.acp.ultimate.template.delete{/lang}" class="icon16 disabled" />
							{/if}
							
							{event name='buttons'}
						</td>
						<td class="columnID"><p>{@$template->templateID}</p></td>
						<td class="columnTitle"><p>{if $__wcf->session->getPermission('admin.content.ultimate.canEditTemplate')}<a title="{lang}wcf.acp.ultimate.template.edit{/lang}" href="{link controller='UltimateTemplateEdit' id=$template->templateID}{/link}">{lang}{@$template->templateTitle}{/lang}</a>{else}{lang}{@$template->templateName}{/lang}{/if}</p></td>
						
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
