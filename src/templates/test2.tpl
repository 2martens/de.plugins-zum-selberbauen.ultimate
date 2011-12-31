{include file='documentHeader'}
<head>
    <title>Test</title>
    {include file='headInclude' sandbox=false}
	<link rel="stylesheet" type="text/css" href="{@RELATIVE_ULTIMATE_DIR}style/ultimate.css" />
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}
<div class="contentHeader"></div>
<div class="ultimateLeft">{@$id1}</div>
<div class="ultimateRight">{@$id1}</div>
<div class="ultimateCenter">{@$id2}</div>

{include file='footer' sandbox=false}

</body>
</html>