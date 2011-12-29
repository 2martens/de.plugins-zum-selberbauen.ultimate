{include file='documentHeader'}
<head>
    <title>Test</title>
    {include file='headInclude' sandbox=false}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">
    {@$id1}
</div>

{include file='footer' sandbox=false}

</body>
</html>