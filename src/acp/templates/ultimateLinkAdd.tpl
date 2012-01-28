{include file='header'}

<script type="text/javascript">
    /* <![CDATA[ */
    $(function() {
        WCF.TabMenu.init();
    });
    /* ]]> */
</script>

<header class="mainHeading">
    <img {if $linkID|isset}id="linkEdit{@$linkID}" {/if}src="{@RELATIVE_WCF_DIR}icon/{@$action}1.svg" alt="" />
    <hgroup>
        <h1>{lang}wcf.acp.ultimate.link.{@$action}{/lang}</h1>
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
            <li><a href="{link controller='UltimateLinkList'}{/link}" title="{lang}wcf.acp.menu.link.ultimate.links.list{/lang}">{*<img src="{@RELATIVE_WCF_DIR}icon/users1.svg" alt="" /> *}<span>{lang}wcf.acp.menu.link.ultimate.links.list{/lang}</span></a></li>
            
            {event name='largeButtons'}
        </ul>
    </nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='UltimateLinkAdd'}{/link}{else}{link controller='UltimateLinkEdit'}{/link}{/if}">
    <div class="border content">
        <dl{if $errorField == 'configID'} class="formError"{/if}>
            <dt><label for="configID">{lang}ultimate.template.link.configTitle{/lang}</label></dt>
            <dd>
                <select id="configID" name="configID" size="1">
                    <option value="0">{lang}ultimate.template.link.configTitle.select{/lang}</option>
                    {foreach from=$configOptions key=$key item=$configTitle}
                    <option value="{$key}"{if $configID == $key} selected="selected"{/if}>{$configTitle}</option>
                    {/foreach}
                </select>
                {if $errorField == 'configID'}
                    <small class="innerError">
                        {lang}ultimate.template.link.configID.error.{@$errorType}{/lang}
                    </small>
                {/if}
            </dd>
        </dl>
        <dl{if $errorField == 'slug'} class="formError"{/if}>
            <dt><label for="slug">{lang}ultimate.template.link.slug{/lang}</label></dt>
            <dd>
                <input type="text" id="slug" name="slug" value="{@$slug}" class="medium" />
                {if $errorField == 'slug'}
                    <small class="innerError">
                        {if $errorType == 'empty'}
                            {lang}wcf.global.form.error.empty{/lang}
                        {else}
                            {lang}ultimate.template.link.slug.error.{@$errorType}{/lang}
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
        {if $linkID|isset}<input type="hidden" name="id" value="{@$linkID}" />{/if}
    </div>
</form>

{include file='footer'}