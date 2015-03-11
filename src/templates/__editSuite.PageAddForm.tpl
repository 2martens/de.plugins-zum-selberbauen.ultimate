<div id="pageContent" data-controller="PageAddForm" data-request-type="form" data-ajax-only="true">
    {if $__wcf->session->getPermission('user.ultimate.editing.canEditPageSpecificRights')}
        {include file='aclPermissions'}
    {/if}
    {include application='ultimate' file='multipleLanguageInputJavascript' elementIdentifier='pageTitle' forceSelection=false}

    {if $__wcf->session->getPermission('user.ultimate.editing.canEditPageSpecificRights')}
        {if $pageID|isset}
            {include file='aclPermissionJavaScript' containerID='userPermissionsContainer' categoryName='user.ultimate.page' objectID=$pageID aclListClassName='ULTIMATE.ACL.List'}
        {else}
            {include file='aclPermissionJavaScript' containerID='userPermissionsContainer' categoryName='user.ultimate.page' aclListClassName='ULTIMATE.ACL.List'}
        {/if}
    {/if}
    <header class="boxHeadline">
        <h1>{lang}wcf.acp.ultimate.page.{@$action}{/lang}</h1>
    </header>
    
    {include file='formError'}
    
    {if $success|isset}
        <p class="success">{lang}wcf.global.success.{@$action}{/lang}</p>
    {/if}
    
    <div class="contentNavigation">
        <nav>
            <ul>
                <li><a data-controller="PageListPage" data-request-type="page" href="{linkExtended application='ultimate' parent='EditSuite' controller='PageList'}{/linkExtended}" title="{lang}wcf.acp.menu.link.ultimate.page.list{/lang}" class="button"><span class="icon icon16 icon-list"></span> <span>{lang}wcf.acp.menu.link.ultimate.page.list{/lang}</span></a></li>
                
                {event name='contentNavigationButtons'}
            </ul>
        </nav>
    </div>
    
    <form method="post" action="{if $action == 'add'}{linkExtended application='ultimate' parent='EditSuite' controller='PageAdd'}{/linkExtended}{else}{linkExtended application='ultimate' parent='EditSuite' controller='PageEdit'}{/linkExtended}{/if}">
        <div class="container containerPadding marginTop shadow">
            <fieldset>
                <legend>{lang}wcf.acp.ultimate.page.general{/lang}</legend>
                <dl{if $errorField == 'pageTitle'} class="formError"{/if}>
                    <dt><label for="pageTitle">{lang}wcf.acp.ultimate.page.title{/lang}</label></dt>
                    <dd>
                        <input type="text" id="pageTitle" name="pageTitle" value="{$I18nPlainValues['pageTitle']}" placeholder="{lang}wcf.acp.ultimate.page.title.placeholder{/lang}" required="required" class="long" />
                        {if $errorField == 'pageTitle'}
                            <small class="innerError">
                                {if $errorType == 'empty'}
                                    {lang}wcf.global.form.error.empty{/lang}
                                {else}
                                    {lang}wcf.acp.ultimate.page.title.error.{@$errorType}{/lang}
                                {/if}
                            </small>
                        {/if}
                    </dd>
                </dl>
                <dl{if $errorField == 'pageSlug'} class="formError"{/if}>
                    <dt><label for="pageSlug">{lang}wcf.acp.ultimate.page.slug{/lang}</label></dt>
                    <dd>
                        <input type="text" id="pageSlug" name="pageSlug" value="{@$pageSlug}" required="required" placeholder="{lang}wcf.acp.ultimate.page.slug.placeholder{/lang}" pattern="^[a-z]+(?:\-{literal}{{/literal}1{literal}}{/literal}[a-zA-Z0-9]+)*(?:\_{literal}{{/literal}1{literal}}{/literal}[a-zA-Z]+(?:\-{literal}{{/literal}1{literal}}{/literal}[a-zA-Z0-9]+)*)*$" class="long" />
                        <small>
                            {lang}wcf.acp.ultimate.page.slug.description{/lang}
                        </small>
                        {if $errorField == 'pageSlug'}
                            <small class="innerError">
                                {if $errorType == 'empty'}
                                    {lang}wcf.global.form.error.empty{/lang}
                                {else}
                                    {lang}wcf.acp.ultimate.page.slug.error.{@$errorType}{/lang}
                                {/if}
                            </small>
                        {/if}
                    </dd>
                </dl>
                {if $__wcf->session->getPermission('user.ultimate.editing.canEditMetadata')}
                    {include file='metaInput' application='ultimate' metaDescription=$metaDescription metaKeywords=$metaKeywords errorField=$errorField errorType=$errorType}
                {/if}
                <dl{if $errorField == 'pageParent'} class="formError"{/if}>
                    <dt><label for="pageParent">{lang}wcf.acp.ultimate.page.parent{/lang}</label></dt>
                    <dd>
                        <select id="pageParent" name="pageParent">
                        <option value="0">{lang}wcf.acp.ultimate.page.parent.none{/lang}</option>
                        {ultimateHtmlOptions options=$pages selected=$pageParent}
                        </select>
                        {if $errorField == 'pageParent'}
                            <small class="innerError">
                                {if $errorType == 'empty'}
                                    {lang}wcf.global.form.error.empty{/lang}
                                {else}
                                    {lang}wcf.acp.ultimate.page.parent.error.{@$errorType}{/lang}
                                {/if}
                            </small>
                        {/if}
                    </dd>
                </dl>
                <dl{if $errorField == 'content'} class="formError"{/if}>
                    <dt><label for="content">{lang}wcf.acp.ultimate.page.content{/lang}</label></dt>
                    <dd>
                        <select id="content" name="content">
                            <option value="0">{lang}wcf.acp.ultimate.page.content.select{/lang}</option>
                            {ultimateHtmlOptions options=$contents selected=$contentID}
                        </select>
                        {if $errorField == 'content'}
                            <small class="innerError">
                                {lang}wcf.acp.ultimate.page.content.error.{@$errorType}{/lang}
                            </small>
                        {/if}
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
                {if $__wcf->session->getPermission('user.ultimate.editing.canEditPageSpecificRights')}
                    <dl id="userPermissionsContainer">
                        <dt><label for="accessMatrix">{lang}wcf.acl.permissions{/lang}</label></dt>
                        <dd>
                            {* access control list *}
                        </dd>
                    </dl>
                {/if}

                {if $__wcf->session->getPermission('user.ultimate.editing.canEditPageStatus') && ($__wcf->session->getPermission('user.ultimate.editing.canSaveAsDraft') || $__wcf->session->getPermission('user.ultimate.editing.canSaveAsPendingReview'))}
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
                {if $pageID|isset}<input type="hidden" name="id" value="{@$pageID}" />{/if}
                {@SECURITY_TOKEN_INPUT_TAG}
            </div>
        {/if}
    </form>
</div>
