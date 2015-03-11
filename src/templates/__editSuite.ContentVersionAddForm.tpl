<div id="pageContent" data-controller="ContentAddForm" data-request-type="form" data-ajax-only="true">
    {include file='wysiwyg'}
    {include application='ultimate' file='multipleLanguageInputJavascript' elementIdentifier='subject' forceSelection=false}
    {include application='ultimate' file='multipleLanguageInputJavascript' elementIdentifier='description' forceSelection=false}
    {include application='ultimate' file='multipleLanguageWYSIWYGJavascript' elementIdentifier='text' forceSelection=false}

    <header class="boxHeadline">
        <h1>{lang}wcf.acp.ultimate.content.version.{@$action}{/lang}</h1>
    </header>

    {include file='formError'}

    {if $success|isset}
        <p class="success">{lang}wcf.global.success.{@$action}{/lang}</p>
    {/if}

    <div class="contentNavigation">
        <nav>
            <ul>
                <li><a data-controller="ContentVersionListPage" data-request-type="page" href="{linkExtended application='ultimate' parent='EditSuite' controller='ContentVersionList' id=$contentID}{/linkExtended}" title="{lang}wcf.acp.menu.link.ultimate.content.version.list{/lang}" class="button"><span class="icon icon16 icon-list"></span> <span>{lang}wcf.acp.menu.link.ultimate.content.version.list{/lang}</span></a></li>
                <li><a data-controller="ContentListPage" data-request-type="page" href="{linkExtended application='ultimate' parent='EditSuite' controller='ContentList'}{/linkExtended}" title="{lang}wcf.acp.menu.link.ultimate.content.list{/lang}" class="button"><span class="icon icon16 icon-list"></span> <span>{lang}wcf.acp.menu.link.ultimate.content.list{/lang}</span></a></li>
                
                {event name='contentNavigationButtons'}
            </ul>
        </nav>
    </div>

    <form method="post" action="{if $action == 'add'}{linkExtended application='ultimate' parent='EditSuite' controller='ContentVersionAdd'}{/linkExtended}{else}{linkExtended application='ultimate' parent='EditSuite' controller='ContentEdit'}{/linkExtended}{/if}">
        <div class="container containerPadding marginTop shadow">
            <fieldset>
                <legend>{lang}wcf.acp.ultimate.content.general{/lang}</legend>
                <dl{if $errorField == 'subject'} class="formError"{/if}>
                    <dt><label for="subject">{lang}wcf.acp.ultimate.content.title{/lang}</label></dt>
                    <dd>
                        <input type="text" id="subject" name="subject" value="{@$I18nPlainValues['subject']}" class="long" required="required" placeholder="{lang}wcf.acp.ultimate.content.title.placeholder{/lang}" pattern=".{literal}{{/literal}4,{literal}}{/literal}" />
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
                        <input type="text" id="description" name="description" value="{@$I18nPlainValues['description']}" class="long" placeholder="{lang}wcf.acp.ultimate.content.description.placeholder{/lang}" pattern=".{literal}{{/literal}4,{literal}}{/literal}" />
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
                {if $__wcf->session->getPermission('user.ultimate.editing.canEditMetadata')}
                    {include file='metaInput' application='ultimate' metaDescription=$metaDescription metaKeywords=$metaKeywords errorField=$errorField errorType=$errorType}
                    
                    {* WCF Tagging *}
                    {foreach from=$availableLanguages key=languageID item=languageName}
                        {if $tagsI18n[$languageID]|isset}
                            {include file='tagInput' application='ultimate' tags=$tagsI18n[$languageID] languageID=$languageID tagInputSuffix=$languageID}
                        {else}
                            {include file='tagInput' application='ultimate' languageID=$languageID tagInputSuffix=$languageID}
                        {/if}
                    {/foreach}

                    <dl id="tagContainerReal" class="jsOnly">
                        <dd>
                            <div id="tagSearchWrap" class="dropdown preInput">
                                {foreach from=$availableLanguages key=languageID item=languageName}
                                    <span id="tagSearchInputWrap{$languageID}" class="dropdown">
                                    <label for="tagSearchInput{$languageID}"><input id="tagSearchInput{$languageID}" class="long" name="tagSearchInput{$languageID}" type="text" value="" /></label>
                                </span>
                                {/foreach}
                            </div>
                            <div id="tagSearchHidden" class="ultimateHidden">

                            </div>
                            <small>{lang}wcf.tagging.tags.description{/lang}</small>
                        </dd>
                    </dl>

                    {* end WCF Tagging *}

                    <script data-relocate="true" type="text/javascript">
                        /* <![CDATA[ */
                        var $availableLanguages = { {implode from=$availableLanguages key=languageID item=languageName}{@$languageID}: '{$languageName}'{/implode} };
                        var $optionValues = { {implode from=$tagsI18n key=languageID item=value}'{@$languageID}': "{$value}"{/implode} };
                        $(function() {
                            new ULTIMATE.Tagging.MultipleLanguageInput('tagContainer', 'tagSearchInput', true, $optionValues, $availableLanguages);
                        });
                        /* ]]> */
                    </script>
                {/if}
            </fieldset>
            <fieldset>
                <legend>{lang}ultimate.edit.wysiwyg{/lang}</legend>
                <dl{if $errorField == 'text'} class="formError"{/if}>
                    <dt><label for="text">{lang}wcf.acp.ultimate.content.text{/lang}</label></dt>
                    <dd>
                        <textarea id="text" name="text" rows="15" cols="40" class="long" >{@$I18nPlainValues['text']}</textarea>

                        {if $errorField == 'text'}
                            <small class="innerError">
                                {if $errorType == 'empty'}
                                    {lang}wcf.global.form.error.empty{/lang}
                                {else}
                                    {lang}wcf.acp.ultimate.content.text.error.{@$errorType}{/lang}
                                {/if}
                            </small>
                        {/if}
                        {include file='messageFormTabs' wysiwygContainerID='text'}
                    </dd>
                </dl>
            </fieldset>
            {if $__wcf->session->getPermission('user.ultimate.editing.canPublish') ||
            $__wcf->session->getPermission('user.ultimate.editing.canSaveAsDraft') ||
            $__wcf->session->getPermission('user.ultimate.editing.canSaveAsPendingReview')}
                <fieldset>
                    <legend>{lang}ultimate.edit.publishing{/lang}</legend>
                    {if $__wcf->session->getPermission('user.ultimate.editing.canPublish')}
                        <dl{if $errorField == 'publishDate'} class="formError"{/if}>
                            <dt><label for="publishDateInput">{lang}wcf.acp.ultimate.publishDate{/lang}</label></dt>
                            <dd>
                                <input type="datetime" id="publishDateInput" name="publishDate" value="{@$publishDate}" readonly="readonly" class="medium" />
                                <script data-relocate="true" type="text/javascript">
                                    /* <![CDATA[*/
                                    $(function() {
                                        new ULTIMATE.EditSuite.Button.Replacement('publishButton', 'publishDateInput', 'publish');
                                    });
                                    /* ]]> */
                                </script>

                                {if $errorField == 'publishDate'}
                                    <small class="innerError">
                                        {if $errorType == 'empty'}
                                            {lang}wcf.global.form.error.empty{/lang}
                                        {else}
                                            {lang}wcf.acp.ultimate.publishDate.error.{@$errorType}{/lang}
                                        {/if}
                                    </small>
                                {/if}
                            </dd>
                        </dl>
                    {/if}

                    {if $__wcf->session->getPermission('user.ultimate.editing.canEditContentStatus') && ($__wcf->session->getPermission('user.ultimate.editing.canSaveAsDraft') || $__wcf->session->getPermission('user.ultimate.editing.canSaveAsPendingReview'))}
                        <dl{if $errorField == 'status'} class="formError"{/if}>
                            <dt><label for="statusSelect">{lang}wcf.acp.ultimate.status{/lang}</label></dt>
                            <dd>
                                <select id="statusSelect" name="status">
                                    {htmlOptions options=$statusOptions selected=$statusID}
                                </select>
                                <script data-relocate="true" type="text/javascript">
                                    /* <![CDATA[ */
                                    $(function() {
                                        new ULTIMATE.EditSuite.Button.Replacement('saveButton', 'statusSelect', 'save');
                                    });
                                    /* ]]> */
                                </script>
                                {if $errorField == 'status'}
                                    <small class="innerError">
                                        {lang}wcf.acp.ultimate.status.error.{@$errorType}{/lang}
                                    </small>
                                {/if}
                            </dd>
                        </dl>
                    {/if}
                </fieldset>
            {/if}
            {event name='fieldsets'}
        </div>

        {if $__wcf->session->getPermission('user.ultimate.editing.canPublish') ||
        $__wcf->session->getPermission('user.ultimate.editing.canSaveAsDraft') ||
        $__wcf->session->getPermission('user.ultimate.editing.canSaveAsPendingReview')}
            <div class="formSubmit">
                <input type="reset" value="{lang}wcf.global.button.reset{/lang}" accesskey="r" />
                {if ($statusID < 2 || $__wcf->session->getPermission('user.ultimate.editing.canDepublish')) && ($__wcf->session->getPermission('user.ultimate.editing.canSaveAsDraft') || $__wcf->session->getPermission('user.ultimate.editing.canSaveAsPendingReview'))}
                    <input type="submit" name="save" id="saveButton" value="{if $saveButtonLang|isset}{@$saveButtonLang}{else}{lang}ultimate.button.saveAsDraft{/lang}{/if}" />
                {/if}
                {if $__wcf->session->getPermission('user.ultimate.editing.canPublish')}
                    <input type="submit" name="publish" id="publishButton" value="{if $publishButtonLang|isset}{@$publishButtonLang}{else}{lang}ultimate.button.publish{/lang}{/if}" accesskey="s" />
                {/if}
                {@SID_INPUT_TAG}
                <input type="hidden" name="action" value="{@$action}" />
                {if $versionID|isset}<input type="hidden" name="id" value="{@$versionID}" />{/if}
                {@SECURITY_TOKEN_INPUT_TAG}
            </div>
        {/if}
    </form>
</div>
