{include file='documentHeader'}
<head>
    <title>{lang}ultimate.template.index.title{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
    {include file='headInclude' sandbox=false}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}
<div class="mainHeadline">
    <div class="headlineContainer">
        <h2>{lang}{lang}ultimate.template.index.title{/lang}</h2>
        <p>{$headlineDescription}</p>
    </div>
</div>
<div class="border content">
    {@$content}
</div>
{include file='footer' sandbox=false}
</body>
</html>