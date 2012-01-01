{include file='header'}

<script type="text/javascript">
    /* <![CDATA[ */
    $(function() {
        WCF.TabMenu.init();
    });
    /* ]]> */
</script>

<header class="mainHeading">
    <img {if $contentID|isset}id="contentEdit{@$contentID}" {/if}src="{@RELATIVE_WCF_DIR}icon/{@$action}1.svg" alt="" />
    <hgroup>
        <h1>{lang}wcf.acp.ultimate.content.{@$action}{/lang}</h1>
    </hgroup>
</header>

{if $errorField}
    <p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $success|isset}
    <p class="success">{lang}wcf.global.form.{@$action}.success{/lang}</p>
{/if}

<div class="contentHeader">
    <nav>
        <ul class="largeButtons">
            <li><a href="{link controller='UltimateContentList'}{/link}" title="{lang}wcf.acp.menu.link.ultimate.contents.list{/lang}">{*<img src="{@RELATIVE_WCF_DIR}icon/users1.svg" alt="" /> *}<span>{lang}wcf.acp.menu.link.ultimate.contents.list{/lang}</span></a></li>
            
            {event name='largeButtons'}
        </ul>
    </nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='UltimateContentAdd'}{/link}{else}{link controller='UltimateContentEdit'}{/link}{/if}">
    <div class="border content">
        <dl{if $errorType.title|isset} class="formError"{/if}>
            <dt><label for="title">{lang}ultimate.template.content.title{/lang}</label></dt>
            <dd>
                <input type="text" id="title" name="title" value="{$title}" class="medium" />
                {if $errorType.title|isset}
                    <small class="innerError">
                        {if $errorType.title == 'empty'}
                            {lang}wcf.global.form.error.empty{/lang}
                        {else}
                            {lang}ultimate.template.content.title.error.{@$errorType.title}{/lang}
                        {/if}
                    </small>
                {/if}
            </dd>
        </dl>
        <dl{if $errorType.description|isset} class="formError"{/if}>
            <dt><label for="description">{lang}ultimate.template.content.description{/lang}</label></dt>
            <dd>
                <input type="text" id="description" name="description" value="{$description}" class="medium" />
                {if $errorType.description|isset}
                    <small class="innerError">
                        {if $errorType.description == 'empty'}
                            {lang}wcf.global.form.error.empty{/lang}
                        {else}
                            {lang}ultimate.template.content.description.error.{@$errorType.description}{/lang}
                        {/if}
                    </small>
                {/if}
            </dd>
        </dl>
        <dl{if $errorType.text|isset} class="formError"{/if}>
            <dt><label for="text">{lang}ultimate.template.content.text{/lang}</label></dt>
            <dd>
                <textare id="text" name="text" class="medium">{@$text}</textarea>
                {if $errorType.text|isset}
                    <small class="innerError">
                        {if $errorType.text == 'empty'}
                            {lang}wcf.global.form.error.empty{/lang}
                        {else}
                            {lang}ultimate.template.content.text.error.{@$errorType.text}{/lang}
                        {/if}
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
        <input type="hidden" name="action" value="{@$action}" />
        {if $contentID|isset}<input type="hidden" name="id" value="{@$contentID}" />{/if}
    </div>
</form>

{include file='footer'}
     