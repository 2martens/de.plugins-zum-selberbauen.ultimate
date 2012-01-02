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
    		{foreach from=$entries['left'] key=$key item=$entry}
    			<div id="left{$key}">{$entry->output}</div>
    		{/foreach}
        </div>
    </div>
    <div class="ultimateRight">
        <div class="sortable" id="columnRight">
    		{foreach from=$entries['right'] key=$key item=$entry}
    			<div id="right{$key}">{$entry->output}</div>
    		{/foreach}
        </div>
    </div>
    <div class="ultimateCenter">
        <div class="sortable" id="columnCenter">
    		{foreach from=$entries['center'] key=$key item=$entry}
    			<div id="center{$key}">{$entry->output}</div>
    		{/foreach}
        </div>
    </div>
</div>
<div class="contentFooter"></div>
{include file='footer' sandbox=false}
</body>
</html>