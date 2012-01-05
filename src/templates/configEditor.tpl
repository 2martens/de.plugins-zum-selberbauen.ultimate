{include file='documentHeader' sandbox=false}
<head>
    <title>{lang}ultimate.template.configEditor.title{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
    <meta name="description" content="A comfortable editor for creating pretty website pages." />
	<meta name="keywords" content="editor, content, config" />
    {include file='headInclude' sandbox=false}
    
    <script type="text/javascript">
    /* <![CDATA[ */
    var column = '';
    var indexLeft = 0;
    var indexCenter = 0;
    var indexRight = 0;
    /* initial */
    $(document).ready(function() {
    	/* initialize sortables */
    	$('.sortable').sortable();
    	$('.sortable').disableSelection();
    	
    	/* initialize button click event */
    	$('#addButtonLeft, #addButtonCenter, #addButtonRight').click(function() {
    		var elementID = $(this).attr('id');
    		column = elementID.substring(9);
    		WCF.showDialog('popupAddEntry', true, {
    			title: '{lang}ultimate.template.addEntry.title{/lang}'
    		});
    		return false;
    	});
    	/* initializing index values */
    	indexLeft = $('#columnLeft').length;
    	indexCenter = $('#columnCenter').length;
    	indexRight = $('#columnCenter').length;
    	
    	/* adding submit handler to popupAddEntry */
    	$('#addEntryForm').submit(function(event) {
    		
    		/* stop form from submitting normally */
    		event.preventDefault();
    		
    		/* get form values */
    		var $form = $(this),
    			url = $form.attr('action'),
    			componentIDValue = $('#componentID').val(),
    			contentIDValue = $('#contentID').val(),
    			sValue = $form.find( 'input[name="s"]' ).val(),
    			tValue = $form.find( 'input[name="t"]' ).val(),
    			formValue = $('#form').val(),
    			ajax = 1;
    		
    		/* Send the data */
    		var jqXHR = $.post(url,
    			{
    				componentID: componentIDValue,
    				contentID: contentIDValue,
    				s: sValue,
    				t: tValue,
    				c: column,
    				form: formValue,
    				ajax: '1'
    			},
    			function(data) {
    				var result = data;
    				var selectorColumn = 'column' + column;
    				$(selectorColumn).append(result);
    				increaseIndex();
    			},
    			'html'
    		);
    		
    	});
    });
    
	function increaseIndex() {
    	if (column == 'Left') indexLeft++;
    	if (column == 'Center') indexCenter++;
    	if (column == 'Right') indexRight++;
    }
    function decreaseIndex() {
    	Uif (column == 'Left') indexLeft--;
    	if (column == 'Center') indexCenter--;
    	if (column == 'Right') indexRight--;
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
{include file='popupAddEntry' sandbox=false}
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
        <input type="reset" value="{lang}wcf.global.button.reset{/lang}" accesskey="r" />
        <input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
        {@SID_INPUT_TAG}
        {@SECURITY_TOKEN_INPUT_TAG}
        <input type="hidden" name="action" value="{@$action}" />
        {if $configID|isset}<input type="hidden" name="id" value="{@$configID}" />{/if}
    </div>

	<div id="columnsParent">
    	<div class="ultimateLeft">
        	<fieldset class="sortableParent">
        		<legend>{lang}ultimate.template.configEditor.columnLeft{/lang}</legend>
    			<div class="sortable" id="columnLeft">
        		{foreach from=$entries['left'] key=$key item=$entry}
    				<div id="left{$key}">{$entry->getContent()}</div>
    			{/foreach}
    			</div>
    		</fieldset>
    		<ul class="largeButtons">
    			<li><a id="addButtonLeft" title="{lang}ultimate.template.configEditor.addEntry{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/add1.svg" alt="" /> <span>{lang}ultimate.template.configEditor.addEntry{/lang}</span></a></li>
    		</ul>
    	</div>
    	<div class="ultimateRight">
    		<fieldset class="sortableParent">
        		<legend>{lang}ultimate.template.configEditor.columnRight{/lang}</legend>
        		<div class="sortable" id="columnRight">
        		{foreach from=$entries['right'] key=$key item=$entry}
    				<div id="right{$key}">{$entry->getContent()}</div>
    			{/foreach}
        		</div>
        	</fieldset>
    		<ul class="largeButtons">
    			<li><a id="addButtonRight" title="{lang}ultimate.template.configEditor.addEntry{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/add1.svg" alt="" /> <span>{lang}ultimate.template.configEditor.addEntry{/lang}</span></a></li>
    		</ul>
    	</div>
    	<div class="ultimateCenter">
       		<fieldset class="sortableParent">
        		<legend>{lang}ultimate.template.configEditor.columnCenter{/lang}</legend>
    			<div class="sortable" id="columnCenter">
       			{foreach from=$entries['center'] key=$key item=$entry}
    				<div id="center{$key}">{$entry->getContent()}</div>
    			{/foreach}
    			</div>
    		</fieldset>
    		<ul class="largeButtons">
    			<li><a id="addButtonCenter" title="{lang}ultimate.template.configEditor.addEntry{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/add1.svg" alt="" /> <span>{lang}ultimate.template.configEditor.addEntry{/lang}</span></a></li>
    		</ul>
    	</div>
	</div>
</form>
<div class="contentFooter"></div>
{include file='footer' sandbox=false}
</body>
</html>