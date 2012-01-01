{include file='documentHeader'}
<head>
    <title>{lang}ultimate.template.configEditor.title{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
    {include file='headInclude' sandbox=false}
    <script type="text/javascript">
    /* <![CDATA[ */
    $("#columnLeft, #columnCenter, #columnRight").sortable({ containment: "#columnsParent" });
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
        <p>{lang}ultimate.template.configEditor.headlineDescription{/lang}</p>
    </div>
</div>
<div id="columnsParent">
    <div class="ultimateLeft">
        <div class="sortable" id="columnLeft">
    
        </div>
    </div>
    <div class="ultimateRight">
        <div class="sortable" id="columnRight">
    
        </div>
    </div>
    <div class="ultimateCenter">
        <div class="sortable" id="columnCenter">
    
        </div>
    </div>
</div>
<div class="contentFooter"></div>
{include file='footer' sandbox=false}
</body>
</html>