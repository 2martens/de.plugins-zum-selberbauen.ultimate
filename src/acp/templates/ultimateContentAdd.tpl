{include file='header'}
{include file='wysiwyg'} 

<header class="boxHeadline">
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

<div class="contentNavigation">
    <nav>
        <ul>
            <li><a href="{link controller='UltimateContentList'}{/link}" title="{lang}wcf.acp.menu.link.ultimate.content.list{/lang}" class="button"><img src="{@$__wcf->getPath()}icon/list.svg" alt="" class="icon24" /> <span>{lang}wcf.acp.menu.link.ultimate.content.list{/lang}</span></a></li>
            
            {event name='largeButtons'}
        </ul>
    </nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='UltimateContentAdd'}{/link}{else}{link controller='UltimateContentEdit'}{/link}{/if}">
    <div class="container containerPadding marginTop shadow">
        <fieldset>
            <legend>{lang}wcf.acp.ultimate.content.general{/lang}</legend>
            <dl{if $errorField == 'subject'} class="formError"{/if}>
                <dt><label for="subject">{lang}wcf.acp.ultimate.content.title{/lang}</label></dt>
                <dd>
                    <script type="text/javascript">
                    //<![CDATA[
                        $(function() {
                            var $availableLanguages = { {implode from=$availableLanguages key=languageID item=languageName}{@$languageID}: '{$languageName}'{/implode} };
                            var $optionValues = { {implode from=$i18nValues['subject'] key=languageID item=value}'{@$languageID}': '{$value}'{/implode} };
                            new WCF.MultipleLanguageInput('subject', false, $optionValues, $availableLanguages);
                        });
                    //]]>
                    </script>
                    <input type="text" id="subject" name="subject" value="{@$i18nPlainValues['subject']}" class="long" autofocus="autofocus" placeholder="{lang}wcf.acp.ultimate.content.title.placeholder{/lang}" />
                    {if $errorField == 'subject'}
                        <small class="innerError">
                            {if $errorType == 'empty'}
                                {lang}wcf.global.form.error.empty{/lang}
                            {else}
                                {lang}wcf.acp.ultimate.content.title.error.{@$errorType}{/lang}
                            {/if}
                        </small>
                    {/if}
                </dd>
            </dl>
            <dl{if $errorField == 'description'} class="formError"{/if}>
                <dt><label for="description">{lang}wcf.acp.ultimate.content.description{/lang}</label></dt>
                <dd>
                    <script type="text/javascript">
                    //<![CDATA[
                        $(function() {
                            var $availableLanguages = { {implode from=$availableLanguages key=languageID item=languageName}{@$languageID}: '{$languageName}'{/implode} };
                            var $optionValues = { {implode from=$i18nValues['description'] key=languageID item=value}'{@$languageID}': '{$value}'{/implode} };
                            new WCF.MultipleLanguageInput('description', false, $optionValues, $availableLanguages);
                        });
                    //]]>
                    </script>
                    <input type="text" id="description" name="description" value="{@$i18nPlainValues['description']}" class="long" placeholder="{lang}wcf.acp.ultimate.content.description.placeholder{/lang}" />
                    {if $errorField == 'description'}
                        <small class="innerError">
                            {if $errorType == 'empty'}
                                {lang}wcf.global.form.error.empty{/lang}
                            {else}
                                {lang}wcf.acp.ultimate.content.description.error.{@$errorType}{/lang}
                            {/if}
                        </small>
                    {/if}
                </dd>
            </dl>
            <dl {if $errorField == 'category'} class="formError"{/if}>
                <dt><label>{lang}wcf.acp.ultimate.content.categories{/lang}</label></dt>
                <dd>
                    {htmlCheckboxes options=$categories name=categoryIDs selected=$categoryIDs}
                    {if $errorField == 'category'}
                        <small class="innerError">
                            {lang}wcf.acp.ultimate.content.categories.error.{@$errorType}{/lang}
                        </small>
                    {/if}
                </dd>
            </dl>
        {*
        </fieldset>
        <fieldset>
            <legend>{lang}wcf.acp.ultimate.content.message{/lang}</legend> *}
            {include file='messageFormMultilingualism'}
            <dl{if $errorField == 'text'} class="formError"{/if}>
                <dt><label for="text">{lang}wcf.acp.ultimate.content.text{/lang}</label></dt>
                <dd>
                    <script type="text/javascript">
                    //<![CDATA[
                        $(function() {
                            var $availableLanguages = { {implode from=$availableLanguages key=languageID item=languageName}{@$languageID}: '{$languageName}'{/implode} };
                            var $optionValues = { {implode from=$i18nValues['text'] key=languageID item=value}'{@$languageID}': "{$value}"{/implode} };
                            new WCF.MultipleLanguageInput('text', false, $optionValues, $availableLanguages);
                        });
                    //]]>
                    </script>
                    <textarea id="text" name="text" rows="15" cols="40" class="long" {* placeholder="{lang}wcf.acp.ultimate.content.text.placeholder{/lang}"*} >{@$i18nPlainValues['text']}</textarea>
                    
                    {if $errorField == 'text'}
                        <small class="innerError">
                            {if $errorType == 'empty'}
                                {lang}wcf.global.form.error.empty{/lang}
                            {else}
                                {lang}wcf.acp.ultimate.content.text.error.{@$errorType}{/lang}
                            {/if}
                        </small>
                    {/if}
                </dd>
            </dl>
        </fieldset>
        {event name='fieldsets'}
        {include file='messageFormTabs'}
    </div>
    
    <div class="formSubmit">
        <input type="reset" value="{lang}wcf.global.button.reset{/lang}" accesskey="r" />
        <input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
        {@SID_INPUT_TAG}
        <input type="hidden" name="action" value="{@$action}" />
       	<input type="hidden" name="parseURL" value="1" />
        <input type="hidden" name="enableSmilies" value="1" />
        <input type="hidden" name="enableHtml" value="0" />
        <input type="hidden" name="enableBBCodes" value="1" />
        {if $contentID|isset}<input type="hidden" name="id" value="{@$contentID}" />{/if}
    </div>
</form>

{include file='footer'} 