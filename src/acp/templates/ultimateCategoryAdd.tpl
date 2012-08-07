{include file='header'}

<script type="text/javascript">
    /* <![CDATA[ */
    $(function() {
        WCF.TabMenu.init();
    });
    /* ]]> */
</script>

<header class="boxHeadline">
    <hgroup>
        <h1>{lang}wcf.acp.ultimate.category.{@$action}{/lang}</h1>
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
            <li><a href="{link controller='UltimateCategoryList'}{/link}" title="{lang}wcf.acp.menu.link.ultimate.category.list{/lang}" class="button"><img src="{@$__wcf->getPath()}icon/list.svg" alt="" class="icon24" /> <span>{lang}wcf.acp.menu.link.ultimate.category.list{/lang}</span></a></li>
            
            {event name='largeButtons'}
        </ul>
    </nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='UltimateCategoryAdd'}{/link}{else}{link controller='UltimateCategoryEdit'}{/link}{/if}">
    <div class="container containerPadding marginTop shadow">
        <fieldset>
            <legend>{lang}wcf.acp.ultimate.category.general{/lang}</legend>
            <dl{if $errorField == 'categoryTitle'} class="wcf-formError"{/if}>
                <dt><label for="categoryTitle">{lang}wcf.acp.ultimate.category.title{/lang}</label></dt>
                <dd>
                    <script type="text/javascript">
                    //<![CDATA[
                        $(function() {
                            var $availableLanguages = { {implode from=$availableLanguages key=languageID item=languageName}{@$languageID}: '{$languageName}'{/implode} };
                            var $optionValues = { {implode from=$i18nValues['categoryTitle'] key=languageID item=value}'{@$languageID}': '{$value}'{/implode} };
                            new WCF.MultipleLanguageInput('categoryTitle', false, $optionValues, $availableLanguages);
                        });
                    //]]>
                    </script>
                    <input type="text" id="categoryTitle" name="categoryTitle" value="{@$i18nPlainValues['categoryTitle']}" class="long" required="required" placeholder="{lang}wcf.acp.ultimate.category.title.placeholder{/lang}" />
                    {if $errorField == 'categoryTitle'}
                        <small class="wcf-innerError">
                            {if $errorType == 'empty'}
                                {lang}wcf.global.form.error.empty{/lang}
                            {else}
                                {lang}wcf.acp.ultimate.category.title.error.{@$errorType}{/lang}
                            {/if}
                        </small>
                    {/if}
                </dd>
            </dl>
            <dl{if $errorField == 'categorySlug'} class="wcf-formError"{/if}>
                <dt><label for="categorySlug">{lang}wcf.acp.ultimate.category.slug{/lang}</label></dt>
            <dd>
                <input type="text" id="categorySlug" name="categorySlug" value="{@$categorySlug}" class="long" required="required" pattern="^[^,\nA-Z]+$" placeholder="{lang}wcf.acp.ultimate.category.slug.placeholder{/lang}" />
                    {if $errorField == 'categorySlug'}
                        <small class="wcf-innerError">
                            {if $errorType == 'empty'}
                                {lang}wcf.global.form.error.empty{/lang}
                            {else}
                                {lang}wcf.acp.ultimate.category.slug.error.{@$errorType}{/lang}
                            {/if}
                        </small>
                    {/if}
                </dd>
            </dl>
            <dl{if $errorField == 'categoryParent'} class="wcf-formError"{/if}>
                <dt><label for="categoryParent">{lang}wcf.acp.ultimate.category.parent{/lang}</label></dt>
                <dd>
                    <select name="categoryParent">
                    <option value="0">{lang}wcf.acp.ultimate.category.parent.none{/lang}</option>
                    {htmloptions options=$categories selected=$categoryParent}
                    </select>
                    {if $errorField == 'categoryParent'}
                        <small class="wcf-innerError">
                            {if $errorType == 'empty'}
                                {lang}wcf.global.form.error.empty{/lang}
                            {else}
                                {lang}wcf.acp.ultimate.category.parent.error.{@$errorType}{/lang}
                            {/if}
                        </small>
                    {/if}
                </dd>
            </dl>
            <dl{if $errorField == 'categoryDescription'} class="wcf-formError"{/if}>
                <dt><label for="categoryDescription">{lang}wcf.acp.ultimate.category.description{/lang}</label></dt>
                <dd>
                    <script type="text/javascript">
                    //<![CDATA[
                        $(function() {
                            var $availableLanguages = { {implode from=$availableLanguages key=languageID item=languageName}{@$languageID}: '{$languageName}'{/implode} };
                            var $optionValues = { {implode from=$i18nValues['categoryDescription'] key=languageID item=value}'{@$languageID}': '{$value}'{/implode} };
                            new WCF.MultipleLanguageInput('categoryDescription', false, $optionValues, $availableLanguages);
                        });
                    //]]>
                    </script>
                    <input type="text" id="categoryDescription" name="categoryDescription" value="{@$i18nPlainValues['categoryDescription']}" class="long" placeholder="{lang}wcf.acp.ultimate.category.description.placeholder{/lang}" />
                    {if $errorField == 'categoryDescription'}
                        <small class="wcf-innerError">
                            {if $errorType == 'empty'}
                                {lang}wcf.global.form.error.empty{/lang}
                            {else}
                                {lang}wcf.acp.ultimate.category.description.error.{@$errorType}{/lang}
                            {/if}
                        </small>
                    {/if}
                </dd>
            </dl>
        </fieldset>    
        {event name='fieldsets'}
    </div>
        
    <div class="formSubmit">
        <input type="reset" value="{lang}wcf.global.button.reset{/lang}" accesskey="r" />
        <input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
        {@SID_INPUT_TAG}
        <input type="hidden" name="action" value="{@$action}" />
        {if $categoryID|isset}<input type="hidden" name="id" value="{@$categoryID}" />{/if}
    </div>
</form>

{include file='footer'}