{include file='documentHeader'}
<head>
    <title>Index</title>
    {include file='headInclude' sandbox=false}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}
<div class="mainHeadline>
    <div class="headlineContainer">
        <h2>Main page</h2>
        <p>The main page</p>
    </div>
</div>
<div class="border content">
    <p>This is the main page.</p>
</div>
{include file='footer' sandbox=false}

</body>
</html>