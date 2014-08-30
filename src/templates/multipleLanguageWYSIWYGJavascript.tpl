{if $availableLanguages|count > 1}
    {capture assign='valuesI18n'}
        {implode from=$I18nValues[$elementIdentifier] key=languageID item=value}'{@$languageID}': '{$value}'{/implode}
    {/capture}
    <script data-relocate="true">
        //<![CDATA[
        $(function() {
            var $availableLanguages = { {implode from=$availableLanguages key=languageID item=languageName}{@$languageID}: '{$languageName}'{/implode} };
            var $values = { {@$valuesI18n} };
            new ULTIMATE.MultipleLanguageWYSIWYG('{@$elementIdentifier}', {if $forceSelection}true{else}false{/if}, $values, $availableLanguages);
        });
        //]]>
    </script>
{/if}
