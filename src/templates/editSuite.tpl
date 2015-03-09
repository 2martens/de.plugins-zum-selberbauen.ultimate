{include file="documentHeader"}
<head>
	<title>{lang}ultimate.edit.suite{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	
	{include file='headInclude'}
</head>

<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
<script data-relocate="true" type="text/javascript">
	//<![CDATA[
		$(function() {
			var $activeMenuItems = [{implode from=$activeMenuItems item=_menuItem}'{$_menuItem}'{/implode}];
			var $sidebarMenu = new ULTIMATE.EditSuite.SidebarMenu($activeMenuItems);
			new ULTIMATE.EditSuite.AJAXLoading('pageContentContainer', 'pageJSContainer', $sidebarMenu);
		});
	//]]>
</script>
{capture assign='sidebar'}
	<div class="menuGroup collapsibleMenus">
		<fieldset>
			<legend class="menuHeader">{lang}ultimate.edit.contents{/lang}</legend>
			<nav class="menuGroupItems">
				<ul id="ultimate.edit.contents">
					<li id="ContentListPage">
						<a data-controller="ContentListPage" data-request-type="page" href="{linkExtended controller='ContentList' application='ultimate' parent='EditSuite'}{/linkExtended}">{lang}ultimate.edit.listContents{/lang}</a>
					</li>
					<li id="ContentAddForm">
						<a data-controller="ContentAddForm" data-request-type="form" href="{linkExtended controller='ContentAdd' application='ultimate' parent='EditSuite'}{/linkExtended}">{lang}ultimate.edit.addContent{/lang}</a>
					</li>
                    <li id="PageListPage">
                        <a data-controller="PageListPage" data-request-type="page" href="{linkExtended controller='PageList' application='ultimate' parent='EditSuite'}{/linkExtended}">{lang}ultimate.edit.listPages{/lang}</a>
                    </li>
                    <li id="PageAddForm">
                        <a data-controller="PageAddForm" data-request-type="form" href="{linkExtended controller='PageAdd' application='ultimate' parent='EditSuite'}{/linkExtended}">{lang}ultimate.edit.addPage{/lang}</a>
                    </li>
				</ul>
			</nav>
		</fieldset>
	</div>
{/capture}

{include file='header' application='ultimate' sidebarOrientation='left' collapsibleMenu='true'}

	<!-- form/page content -->
	{include file='userNotice'}
	<div id="pageJSContainer">
		{@$pageJS}
	</div>
	<div id="pageContentContainer" data-initial-controller="{$initialController}" data-initial-request-type="{$initialRequestType}" data-initial-url="{$initialURL}">
		{@$pageContent}
	</div>
	<!-- /form/page content -->

{include file='footer' application='ultimate'}

</body>
</html>
