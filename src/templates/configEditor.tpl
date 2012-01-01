{include file='documentHeader'}
<head>
    <title>{lang}ultimate.template.configEditor.title{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
    {include file='headInclude' sandbox=false}
    <script type="text/javascript">
    /* <![CDATA[ */
    $("#columnLeft, #columnCenter, #columnRight").sortable({containment: $("#columnsParent")});
    $("#columnLeft, #columnCenter, #columnRight").disableSelection();
    /* ]]> */
    </script>
</head>
<body {if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}
<div class="contentHeader"></div>
<div class="mainHeadline">
    <div class="headlineContainer">
        <h2>{lang}ultimate.template.configEditor.title{/lang}</h2>
        <p>{lang}ultimat.template.configEditor.headlineDescription{/lang}</p>
    </div>
</div>
<div id="columnsParent">
    <ul class="sortable ultimateLeft" id="columnLeft">
    
    </ul>
    <ul class="sortable ultimateRight" id="columnRight">
    
    </ul>
    <ul class="sortable ultimateCenter" id="columnCenter">
    
    </ul>
</div>
<div class="contentFooter"></div>
{include file='footer' sandbox=false}
</body>
</html>