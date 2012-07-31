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
        <h1>{lang}wcf.acp.ultimate.page.{@$action}{/lang}</h1>
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
            <li><a href="{link controller='UltimatePageList'}{/link}" title="{lang}wcf.acp.menu.link.ultimate.page.list{/lang}" class="button"><img src="{@$__wcf->getPath()}icon/list.svg" alt="" class="icon24" /> <span>{lang}wcf.acp.menu.link.ultimate.page.list{/lang}</span></a></li>
            
            {event name='largeButtons'}
        </ul>
    </nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='UltimatePageAdd'}{/link}{else}{link controller='UltimatePageEdit'}{/link}{/if}">
    <div class="container containerPadding marginTop shadow">
        <fieldset>
            <legend>{lang}wcf.acp.ultimate.page.general{/lang}</legend>
            <dl{if $errorField == 'pageTitle'} class="formError"{/if}>
                <dt><label for="pageTitle">{lang}wcf.acp.ultimate.page.title{/lang}</label></dt>
                <dd>
                    <script type="text/javascript">
                    //<![CDATA[
                        $(function() {
                            var $availableLanguages = { {implode from=$availableLanguages key=languageID item=languageName}{@$languageID}: '{$languageName}'{/implode} };
                            var $optionValues = { {implode from=$i18nValues['pageTitle'] key=languageID item=value}'{@$languageID}': '{$value}'{/implode} };
                            new WCF.MultipleLanguageInput('pageTitle', false, $optionValues, $availableLanguages);
                        });
                    //]]>
                    </script>
                    <input type="text" id="pageTitle" name="pageTitle" value="{$i18nPlainValues['pageTitle']}" placeholder="{lang}wcf.acp.ultimate.page.title.placeholder{/lang}" required="required" autofocus="autofocus" class="long" />
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
                    <input type="text" id="pageSlug" name="pageSlug" value="{@$pageSlug}" required="required" placeholder="{lang}wcf.acp.ultimate.page.slug.placeholder{/lang}" pattern="^[a-z]+(\-{1}[a-z]+)*$" class="long" />
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
            <dl{if $errorField == 'pageParent'} class="formError"{/if}>
                <dt><label for="pageParent">{lang}wcf.acp.ultimate.page.parent{/lang}</label></dt>
                <dd>
                    <select name="pageParent">
                    <option value="0">{lang}wcf.acp.ultimate.page.parent.none{/lang}</option>
                    {htmloptions options=$pages selected=$pageParent}
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
                    <select name="content">
                    <option value="0">{lang}wcf.acp.ultimate.page.content.select{/lang}</option>
                    {htmloptions options=$contents selected=$contentID}
                    </select>
                    {if $errorField == 'content'}
                        <small class="innerError">
                            {lang}wcf.acp.ultimate.page.content.error.{@$errorType}{/lang}
                        </small>
                    {/if}
                </dd>
            </dl>
        </fieldset>
        <fieldset>
            <legend>{lang}wcf.acp.ultimate.publish{lang}</legend>
            <dl{if $errorField == 'status'} class="formError"{/if}>
                <dt><label for="status">{lang}wcf.acp.ultimate.page.status{/lang}</label></dt>
                <dd>
                    <select name="status">
                    {htmloptions options=$statusOptions selected=$statusID}
                    </select>
                    {if $errorField == 'status'}
                        <small class="innerError">
                            {lang}wcf.acp.ultimate.page.status.error.{@$errorType}{/lang}
                        </small>
                    {/if}
                </dd>
            </dl>
            <dl{if $errorField == 'visibility'} class="formError"{/if}>
                <dt><label for="visibility">{lang}wcf.acp.ultimate.page.visibility{/lang}</label></dt>
                <dd>
                    <select id="selectVisibility" name="visibility">
                    <option value="public"{if $visibility == 'public'} selected="selected"{/if}>{lang}wcf.acp.ultimate.page.visibility.public{/lang}</option>
                    <option value="protected"{if $visibility == 'protected'} selected="selected"{/if}>{lang}wcf.acp.ultimate.page.visibility.protected{/lang}</option>
                    <option value="private"{if $visibility == 'private'} selected="selected"{/if}>{lang}wcf.acp.ultimate.page.visibility.private{/lang}</option>
                    </select>
                    <div id="groupCheckboxes" style="display: none;">
                        {htmlcheckboxes name="groupIDs" options=$groups selected=$groupIDs}
                        {if $errorField == 'groupIDs'}
                        <small class="innerError">
                            {lang}wcf.acp.ultimate.page.visibility.groupIDs.error.{@$errorType}{/lang}
                        </small>
                        {/if}
                    </div>
                    <script type="text/javascript">
                    /* <![CDATA[ */
                    $(function() {
                        $('#selectVisibility').change(function () {
                            var selectedIndex = $('#selectVisibility option:selected').attr('value');
                            if (selectedIndex == 'protected') {
                                $('#groupCheckboxes').fadeIn(1000, 'swing');
                            } else {
                                $('#groupCheckboxes').fadeOut(1000, 'swing');
                            }
                        });
                    });
                    /* ]]> */
                    </script>
                    {if $errorField == 'visibility'}
                        <small class="innerError">
                            {lang}wcf.acp.ultimate.page.visibility.error.{@$errorType}{/lang}
                        </small>
                    {/if}
                    <small>{lang}wcf.acp.ultimate.page.visibility.description{/lang}</small>
                </dd>
            </dl>
            <dl{if $errorField == 'publishDate'} class="formError"{/if}>
                <dt><label for="publishDate">{lang}wcf.acp.ultimate.page.publishDate{/lang}</label></dt>
                <dd>
                    <input type="datetime" id="publishDateInput" name="publishDate" value="{@$publishDate}" class="long" required="required" />
                    <script type="text/javascript">
                    /* <![CDATA[*/
                    $(function() {
                        $.timepicker.setDefaults( $.timepicker.regional[ "{if $__wcf->getLanguage()->languageCode == 'en'}en-GB{else}{@$__wcf->getLanguage()->languageCode}{/if}" ] );
                        $.datepicker.setDefaults( $.datepicker.regional[ "{if $__wcf->getLanguage()->languageCode == 'en'}en-GB{else}{@$__wcf->getLanguage()->languageCode}{/if}" ] );
                        $('#publishDateInput').datetimepicker( {
                            showOn: 'button',
                            buttonImage: {icon}calendar.gif{/icon},
                            buttonImageOnly: true,
                            buttonText: '{lang}wcf.acp.ultimate.page.publishDate.editDate{/lang}',
                            showOtherMonths: true,
                            selectOtherMonths: true,
                            showAnim: 'fadeIn',
                            timeFormat: 'hh:mm'
                        } );
                        var $dateFormat = $('#publishDateInput').datetimepicker( 'option', 'dateFormat');
                        $('#dateFormatInput').val($dateFormat);
                        $('form').submit(function() {
                            $('#publishDateInput').datetimepicker( 'option', 'dateFormat', 'yy-mm-dd' );
                        });
                    });
                    /* ]]> */
                    </script>
                    {if $errorField == 'publishDate'}
                        <small class="innerError">
                            {if $errorType == 'empty'}
                                {lang}wcf.global.form.error.empty{/lang}
                            {else}
                                {lang}wcf.acp.ultimate.page.publishDate.error.{@$errorType}{/lang}
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
        <input type="submit" name="save" id="saveButton" value="{lang}ultimate.button.saveAsDraft{/lang}" />
        <input type="submit" name="publish" id="publishButton" value="{lang}ultimate.button.publish{/lang}" accesskey="s" />
        {@SID_INPUT_TAG}
        <input type="hidden" name="startTime" value="{@$startTime}" />
        <input type="hidden" id="dateFormatInput" name="dateFormat" value="yy-mm-dd" />
        <input type="hidden" name="action" value="{@$action}" />
        {if $pageID|isset}<input type="hidden" name="id" value="{@$pageID}" />{/if}
    </div>
</form>

{include file='footer'}