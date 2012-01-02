{include file='documentHeader'}
<head>
    <title>{lang}ultimate.template.configEditor.title{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
    {include file='headInclude' sandbox=false}
    <script type="text/javascript">
    /* <![CDATA[ */
    $(".sortable").sortable({ containment: "#columnsParent" });
    $(".sortable").disableSelection();
    function addEntry($column) {
    	ULTIMATE.ConfigEditor.addEntry($column);
    }
    /* ]]> */
    </script>
</head>
<body {if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<header class="mainHeading">
    <img {if $linkID|isset}id="linkEdit{@$linkID}" {/if}src="{@RELATIVE_WCF_DIR}icon/{@$action}1.svg" alt="" />
    <hgroup>
        <h1>{lang}ultimate.template.configEditor.{@$action}{/lang}</h1>
    </hgroup>
</header>

{if $errorField}
    <p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $success|isset}
    <p class="success">{lang}wcf.global.form.{@$action}.success{/lang}</p>
{/if}

<div class="contentHeader"></div>

<form method="post" action="{if $action == 'add'}{link controller='ConfigEditor'}{/link}{else}{link controller='ConfigEditor'}{/link}{/if}">
	<div class="border content">
        <dl{if $errorType.configTitle|isset} class="formError"{/if}>
            <dt><label for="configTitle">{lang}ultimate.template.configEditor.configTitle{/lang}</label></dt>
            <dd>
                <input type="text" id="configTitle" name="configTitle" value="{@$configTitle}" class="medium" />
                {if $errorType.configTitle|isset}
                    <small class="innerError">
                        {if $errorType.configTitle == 'empty'}
                        	{lang}wcf.global.form.error.empty{/lang}
                        {else}
                        	{lang}ultimate.template.configEditor.configTitle.error.{@$errorType.configTitle}{/lang}
                    	{/if}
                    </small>
                {/if}
            </dd>
        </dl>
        <dl{if $errorType.metaDescription|isset} class="formError"{/if}>
            <dt><label for="metaDescription">{lang}ultimate.template.configEditor.metaDescription{/lang}</label></dt>
            <dd>
                <input type="text" id="metaDescription" name="metaDescription" value="{@$metaDescription}" class="medium" />
                {if $errorType.metaDescription|isset}
                    <small class="innerError">
                        {lang}ultimate.template.configEditor.metaDescription.error.{@$errorType.metaDescription}{/lang}
                    </small>
                {/if}
            </dd>
        </dl>
        <dl{if $errorType.metaKeywords|isset} class="formError"{/if}>
            <dt><label for="metaKeywords">{lang}ultimate.template.configEditor.metaKeywords{/lang}</label></dt>
            <dd>
                <input type="text" id="metaKeywords" name="metaKeywords" value="{@$metaKeywords}" class="medium" />
                {if $errorType.metaKeywords|isset}
                    <small class="innerError">
                        {lang}ultimate.template.configEditor.metaKeywords.error.{@$errorType.metaKeywords}{/lang}
                    </small>
                {/if}
            </dd>
        </dl>
        
        {event name='fieldsets'}
    </div>
	
	<div class="formSubmit">
        <input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
        {@SID_INPUT_TAG}
        {@SECURITY_TOKEN_INPUT_TAG}
        <input type="hidden" name="action" value="{@$action}" />
        {if $configID|isset}<input type="hidden" name="id" value="{@$configID}" />{/if}
    </div>

	<div id="columnsParent">
    	<div class="ultimateLeft">
        	<fieldset>
        		<legend>{lang}ultimate.template.configEditor.columnLeft{/lang}</legend>
    			<div class="sortable" id="columnLeft">
        		{foreach from=$entries['left'] key=$key item=$entry}
    				<div id="left{$key}">{$entry->output}</div>
    			{/foreach}
    			</div>
    			<nav>
    				<ul class="largeButtons">
    					<li><a title="{lang}ultimate.template.configEditor.addEntry{/lang} href="javascript:addEntry('left')"><img src="{@RELATIVE_WCF_DIR}icon/add1.svg" alt="" /> <span>{lang}ultimate.template.configEditor.addEntry{/lang}</span></a></li>
    				</ul>
    			</nav>
    		</fieldset>
    	</div>
    	<div class="ultimateRight">
    		<fieldset>
        		<legend>{lang}ultimate.template.configEditor.columnRight{/lang}</legend>
        		<div class="sortable" id="columnRight">
        		{foreach from=$entries['right'] key=$key item=$entry}
    				<div id="right{$key}">{$entry->output}</div>
    			{/foreach}
        		</div>
        		<nav>
    				<ul class="largeButtons">
    					<li><a title="{lang}ultimate.template.configEditor.addEntry{/lang} href="javascript:addEntry('right')"><img src="{@RELATIVE_WCF_DIR}icon/add1.svg" alt="" /> <span>{lang}ultimate.template.configEditor.addEntry{/lang}</span></a></li>
    				</ul>
    			</nav>
        	</fieldset>
    	</div>
    	<div class="ultimateCenter">
       		<fieldset>
        		<legend>{lang}ultimate.template.configEditor.columnCenter{/lang}</legend>
    			<div class="sortable" id="columnCenter">
       			{foreach from=$entries['center'] key=$key item=$entry}
    				<div id="center{$key}">{$entry->output}</div>
    			{/foreach}
    			</div>
    			<nav>
    				<ul class="largeButtons">
    					<li><a title="{lang}ultimate.template.configEditor.addEntry{/lang} href="javascript:addEntry('center')"><img src="{@RELATIVE_WCF_DIR}icon/add1.svg" alt="" /> <span>{lang}ultimate.template.configEditor.addEntry{/lang}</span></a></li>
    				</ul>
    			</nav>
    		</fieldset>
    	</div>
	</div>
</form>
<div class="contentFooter"></div>
{include file='footer' sandbox=false}
</body>
</html>