{capture assign='pageTitle'}{lang}wcf.acp.ultimate.widgetArea.{@$action}{/lang}{/capture}
{include file='header' application='ultimate'}

<script data-relocate="true" type="text/javascript">
	/* <![CDATA[ */
	$(function() {
		WCF.TabMenu.init();
	});
	/* ]]> */
</script>

<header class="boxHeadline">
	<h1>{lang}wcf.acp.ultimate.widgetArea.{@$action}{/lang}</h1>
</header>

{if $errorField}
	<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{@$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link application='ultimate' controller='UltimateWidgetAreaList'}{/link}" title="{lang}wcf.acp.menu.link.ultimate.appearance.widgetArea.list{/lang}" class="button"><span class="icon icon24 icon-list"></span> <span>{lang}wcf.acp.menu.link.ultimate.appearance.widgetArea.list{/lang}</span></a></li>
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

{if $action == 'add'}
	<p class="info">{lang}wcf.acp.ultimate.widgetArea.addAreaFirst{/lang}</p>
{/if}
<form method="post" action="{if $action == 'add'}{link application='ultimate' controller='UltimateWidgetAreaAdd'}{/link}{else}{link application='ultimate' controller='UltimateWidgetAreaEdit'}{/link}{/if}">
	<div class="container containerPadding marginTop shadow">
		<fieldset>
			<legend>{lang}wcf.acp.ultimate.widgetArea.general{/lang}</legend>
			<dl{if $errorField == 'widgetAreaName'} class="formError"{/if}>
				<dt><label for="widgetAreaName">{lang}wcf.acp.ultimate.widgetArea.name{/lang}</label></dt>
				<dd>
					<input type="text" id="widgetAreaName" name="widgetAreaName" value="{$widgetAreaName}" class="long" required="required" placeholder="{lang}wcf.acp.ultimate.widgetArea.name.placeholder{/lang}" />
					{if $errorField == 'widgetAreaName'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.ultimate.widgetArea.name.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
		</fieldset>
		{event name='fieldsets'}
	</div>
	
	<div class="formSubmit">
		<input type="reset" value="{lang}wcf.global.button.reset{/lang}" accesskey="r" />
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{@SID_INPUT_TAG}
		<input type="hidden" name="action" value="{@$action}" />
		{if $widgetAreaID|isset}<input type="hidden" name="id" value="{@$widgetAreaID}" />{/if}
	</div>
</form>

{if $action == 'edit'}
	<p class="info">{lang}wcf.acp.dashboard.box.sort{/lang}</p>
	
	<div class="tabMenuContainer">
		<nav class="tabMenu">
			<ul>
				{if $objectType->allowsidebar}
					<li><a href="{@$__wcf->getAnchor('dashboard-sidebar')}">{lang}wcf.dashboard.boxType.sidebar{/lang}</a></li>
				{/if}
				
				{event name='tabMenuTabs'}
			</ul>
		</nav>
		
		{if $objectType->allowsidebar}
			<div id="dashboard-sidebar" class="container containerPadding tabMenuContent hidden">
				<fieldset>
					<legend>{lang}wcf.dashboard.box.enabledBoxes{/lang}</legend>
					
					<div class="container containerPadding sortableListContainer">
						<ol class="sortableList simpleSortableList" data-object-id="0">
							{foreach from=$enabledBoxes item=boxID}
								{if $boxes[$boxID]->boxType == 'sidebar'}
									<li class="sortableNode" data-object-id="{@$boxID}">
										<span class="sortableNodeLabel">{lang}wcf.dashboard.box.{$boxes[$boxID]->boxName}{/lang}{if $boxes[$boxID]->packageID != 1} ({lang}{$boxes[$boxID]->getPackage()->packageName}{/lang}){/if}</span>
									</li>
								{/if}
							{/foreach}
						</ol>
					</div>
				</fieldset>
				
				<fieldset>
					<legend>{lang}wcf.dashboard.box.availableBoxes{/lang}</legend>
					
					<div id="dashboard-sidebar-enabled" class="container containerPadding sortableListContainer">
						<ol class="sortableList simpleSortableList">
							{foreach from=$boxes item=box}
								{if $box->boxType == 'sidebar' && !$box->boxID|in_array:$enabledBoxes}
									<li class="sortableNode" data-object-id="{@$box->boxID}">
										<span class="sortableNodeLabel">{lang}wcf.dashboard.box.{$box->boxName}{/lang}{if $box->packageID != 1} ({lang}{$box->getPackage()->packageName}{/lang}){/if}</span>
									</li>
								{/if}
							{/foreach}
						</ol>
					</div>
				</fieldset>
				
				<div class="formSubmit">
					<button data-type="submit">{lang}wcf.global.button.saveSorting{/lang}</button>
				</div>
				
				<script data-relocate="true" type="text/javascript">
					//<![CDATA[
					$(function() {
						new WCF.Sortable.List('dashboard-sidebar', 'ultimate\\data\\widget\\area\\WidgetAreaAction', 0, { }, true, { boxType: 'sidebar', widgetAreaID: {@$widgetAreaID} });
					});
					//]]>
				</script>
			</div>
		{/if}
		
		{event name='tabMenuContents'}
	</div>
{/if}

{include file='footer'}