{include file='documentHeader'}
<head>
    <title>Test</title>
    {include file='headInclude' sandbox=false}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

{@$id1}

{@$id2}
{include file='footer' sandbox=false}

</body>
</html>