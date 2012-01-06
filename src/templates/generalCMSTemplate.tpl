{include file='documentHeader'}
<head>
    <title>{@$configTitle}</title>
    <meta name="description" content="{@$metaDescription}" />
    <meta name="keywords" content="{@$metaKeywords}" />
    {include file='headInclude'}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

{@$entries}

{include file='footer' sandbox=false}
</body>
</html>