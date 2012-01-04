{include file='documentHeader'}
<head>
    <title>{lang}ultimate.template.index.title{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
    <meta name="description" content="{@ULTIMATE_GENERAL_INDEX_META_DESCRIPTION}" />
	<meta name="keywords" content="{@ULTIMATE_GENERAL_INDEX_META_KEYWORDS}" />
    
    {include file='headInclude' sandbox=false}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}
<div class="mainHeadline">
    <div class="headlineContainer">
        <h2>{lang}ultimate.template.index.title{/lang}</h2>
        <p>{if 'ULTIMATE_GENERAL_INDEX_DESCRIPTION'|defined}{@ULTIMATE_GENERAL_INDEX_DESCRIPTION}{else}{/if}</p>
    </div>
</div>
<div class="border content">
    {@$content}
</div>
{include file='footer' sandbox=false}
</body>
</html>