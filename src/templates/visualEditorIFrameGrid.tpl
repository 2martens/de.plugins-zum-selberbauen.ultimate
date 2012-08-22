<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta content="no-cache" http-equiv="cache-control" />
		<title>{PAGE_TITLE}</title>
		
		<script type="text/javascript" src="{@$__wcf->getPath()}js/3rdParty/jquery.min.js"></script>
		<script type="text/javascript" src="{@$__wcf->getPath()}js/3rdParty/jquery-ui.min.js"></script>
		<script type="text/javascript" src="{@$__wcf->getPath()}js/3rdParty/jquery.tools.min.js"></script>
		<script type="text/javascript" src="{@$__wcf->getPath()}js/3rdParty/jquery-ui.nestedSortable.js"></script>
		<script type="text/javascript" src="{@$__wcf->getPath()}js/WCF.js"></script>
		<script type="text/javascript" src="{@$__wcf->getPath('ultimate')}js/ULTIMATE.js"></script>
		<script type="text/javascript">
		//<![CDATA[
			WCF.User.init({@$__wcf->user->userID}, '{@$__wcf->user->username|encodeJS}');
		//]]>
		</script>
		
		<link rel="stylesheet/less" type="text/css" href="{@$__wcf->getPath()}style/bootstrap.less" />
		<link rel="stylesheet" type="text/css" href="{@$__wcf->getPath()}style/3rdParty/jquery-ui.css" />
		<link rel="stylesheet" type="text/css" href="{@$__wcf->getPath()}style/ultimateCore.css" />
		<link rel="stylesheet" type="text/css" href="{@$__wcf->getPath('ultimate')}style/ultimate.css" />
		<link rel="stylesheet" type="text/css" href="{@$__wcf->getPath('ultimate')}style/bootstrapIFrameGrid.css" />
		{event name='stylesheetInclude'}
		<script type="text/javascript">
		//<![CDATA[
			var less = { env: 'development' };
		//]]>
		</script>
		<script type="text/javascript" src="{@$__wcf->getPath()}js/3rdParty/less.min.js"></script>
		
		<script type="text/javascript">
		/* <![CDATA[ */
		$(function() {
			WCF.Language.addObject({
				'wcf.global.button.add': '{lang}wcf.global.button.add{/lang}',
				'wcf.global.button.cancel': '{lang}wcf.global.button.cancel{/lang}',
				'wcf.global.button.collapsible': '{lang}wcf.global.button.collapsible{/lang}',
				'wcf.global.button.delete': '{lang}wcf.global.button.delete{/lang}',
				'wcf.global.button.disable': '{lang}wcf.global.button.disable{/lang}',
				'wcf.global.button.disabledI18n': '{lang}wcf.global.button.disabledI18n{/lang}',
				'wcf.global.button.edit': '{lang}wcf.global.button.edit{/lang}',
				'wcf.global.button.enable': '{lang}wcf.global.button.enable{/lang}',
				'wcf.global.button.next': '{lang}wcf.global.button.next{/lang}',
				'wcf.global.button.preview': '{lang}wcf.global.button.preview{/lang}',
				'wcf.global.button.reset': '{lang}wcf.global.button.reset{/lang}',
				'wcf.global.button.save': '{lang}wcf.global.button.save{/lang}',
				'wcf.global.button.search': '{lang}wcf.global.button.search{/lang}',
				'wcf.global.button.submit': '{lang}wcf.global.button.submit{/lang}',
				'wcf.global.error.title': '{lang}wcf.global.error.title{/lang}',
				'wcf.global.loading': '{lang}wcf.global.loading{/lang}',
				'wcf.date.relative.minutes': '{capture assign=relativeMinutes}{lang}wcf.date.relative.minutes{/lang}{/capture}{@$relativeMinutes|encodeJS}',
				'wcf.date.relative.hours': '{capture assign=relativeHours}{lang}wcf.date.relative.hours{/lang}{/capture}{@$relativeHours|encodeJS}',
				'wcf.date.relative.pastDays': '{capture assign=relativePastDays}{lang}wcf.date.relative.pastDays{/lang}{/capture}{@$relativePastDays|encodeJS}',
				'wcf.date.dateTimeFormat': '{lang}wcf.date.dateTimeFormat{/lang}',
				'__days': [ '{lang}wcf.date.day.sunday{/lang}', '{lang}wcf.date.day.monday{/lang}', '{lang}wcf.date.day.tuesday{/lang}', '{lang}wcf.date.day.wednesday{/lang}', '{lang}wcf.date.day.thursday{/lang}', '{lang}wcf.date.day.friday{/lang}', '{lang}wcf.date.day.saturday{/lang}' ],
				'wcf.global.thousandsSeparator': '{capture assign=thousandsSeparator}{lang}wcf.global.thousandsSeparator{/lang}{/capture}{@$thousandsSeparator|encodeJS}',
				'wcf.global.decimalPoint': '{capture assign=decimalPoint}{lang}wcf.global.decimalPoint{/lang}{/capture}{$decimalPoint|encodeJS}',
				'wcf.global.page.next': '{capture assign=pageNext}{lang}wcf.global.page.next{/lang}{/capture}{@$pageNext|encodeJS}',
				'wcf.global.page.previous': '{capture assign=pagePrevious}{lang}wcf.global.page.previous{/lang}{/capture}{@$pagePrevious|encodeJS}',
				'wcf.global.confirmation.cancel': '{lang}wcf.global.confirmation.cancel{/lang}',
				'wcf.global.confirmation.confirm': '{lang}wcf.global.confirmation.confirm{/lang}',
				'wcf.global.confirmation.title': '{lang}wcf.global.confirmation.title{/lang}',
				'wcf.sitemap.title': '{lang}wcf.sitemap.title{/lang}'
				{event name='javascriptLanguageImport'}
			});
		
			WCF.Icon.addObject({
				'wcf.icon.loading': '{icon size='S'}spinner{/icon}',
				'wcf.icon.opened': '{icon size='S'}arrowDownInverse{/icon}',
				'wcf.icon.closed': '{icon size='S'}arrowRightInverse{/icon}',
				'wcf.icon.arrow.left': '{icon size='S'}arrowLeft{/icon}',
				'wcf.icon.arrow.left.circle': '{icon size='S'}circleArrowLeft{/icon}',
				'wcf.icon.arrow.right': '{icon size='S'}arrowRight{/icon}',
				'wcf.icon.arrow.right.circle': '{icon size='S'}circleArrowRight{/icon}',
				'wcf.icon.arrow.down': '{icon size='S'}arrowDown{/icon}',
				'wcf.icon.arrow.down.circle': '{icon size='S'}circleArroDown{/icon}',
				'wcf.icon.arrow.up': '{icon size='S'}arrowUp{/icon}',
				'wcf.icon.arrow.up.circle': '{icon size='S'}circleArrowUp{/icon}',
				'wcf.icon.dropdown': '{icon size='S'}dropdown{/icon}',
				'wcf.icon.edit': '{icon size='S'}edit{/icon}'
				{event name='javascriptIconImport'}
			});
		
			new WCF.Date.Time();
			new WCF.Effect.BalloonTooltip();
			WCF.Dropdown.init();
		
			{event name='javascriptInit'}
		});
		/* ]]> */
		</script>
		
	</head>
	<body class="visualEditorIFrameGrid">
		<div id="whitewrap">
			<div class="wrapper fixed-grid grid-active" id="wrapper-1">
				<div class="grid-{@$gridColor}" id="grid">
					<div class="grid-column grid-width-1"></div>
					<div class="grid-column grid-width-1"></div>
					<div class="grid-column grid-width-1"></div>
					<div class="grid-column grid-width-1"></div>
					<div class="grid-column grid-width-1"></div>
					<div class="grid-column grid-width-1"></div>
					<div class="grid-column grid-width-1"></div>
					<div class="grid-column grid-width-1"></div>
					<div class="grid-column grid-width-1"></div>
					<div class="grid-column grid-width-1"></div>
					<div class="grid-column grid-width-1"></div>
					<div class="grid-column grid-width-1"></div>
					<div class="grid-column grid-width-1"></div>
					<div class="grid-column grid-width-1"></div>
					<div class="grid-column grid-width-1"></div>
					<div class="grid-column grid-width-1"></div>
					<div class="grid-column grid-width-1"></div>
					<div class="grid-column grid-width-1"></div>
					<div class="grid-column grid-width-1"></div>
					<div class="grid-column grid-width-1"></div>
					<div class="grid-column grid-width-1"></div>
					<div class="grid-column grid-width-1"></div>
					<div class="grid-column grid-width-1"></div>
					<div class="grid-column grid-width-1"></div>
				</div>
				<div class="grid-container ui-grid">

				</div>
				<div id="grid-height-buttons">
					<span class="grid-height-adjustment jsTooltip wcf-badge" id="grid-height-decrease" title="{lang}ultimate.visualEditor.decreaseHeight{/lang}">-</span>
					<span class="grid-height-adjustment jsTooltip wcf-badge" id="grid-height-increase" title="{lang}ultimate.visualEditor.increaseHeight{/lang}">+</span>
				</div>
			</div>
		</div>
	</body>
</html>