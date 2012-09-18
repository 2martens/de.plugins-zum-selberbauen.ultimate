{include file="documentHeader"}
<head>
	<meta charset="utf-8" />
	<title>{PAGE_TITLE}</title>
		
	{include file='headInclude'}
	<link rel="stylesheet" type="text/css" href="{@$__wcf->getPath('ultimate')}style/bootstrapIFrameGrid.css" />
</head>
<body class="visualEditorIFrameGrid" {if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
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