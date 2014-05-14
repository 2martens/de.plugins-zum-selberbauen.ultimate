{include file="documentHeader"}
<head>
	<title>{lang}ultimate.edit.suite{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	
	{include file='headInclude' application='ultimate'}
	{* for debug purposes only *}
	{include file='__editSuiteJS' application='ultimate'}
</head>

<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
<script data-relocate="true" type="text/javascript">
	//<![CDATA[
		$(function() {
			var $activeMenuItems = [{implode from=$activeMenuItems item=_menuItem}'{$_menuItem}'{/implode}];
			var $sidebarMenu = new ULTIMATE.EditSuite.SidebarMenu($activeMenuItems);
			new ULTIMATE.EditSuite.AJAXLoading('pageContentContainer', 'pageJSContainer', $sidebarMenu);
			if (typeof(initPage) === 'function') {
				initPage();
			}
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
	<div id="pageContentContainer" data-initial-controller="{$initialController}" data-initial-request-type="{$initialRequestType}">
		{@$pageContent}
	</div>
	<!-- /form/page content -->

{include file='footer' application='ultimate'}

</body>
</html>