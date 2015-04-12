{if $availableLanguages|count > 1}
    {capture assign='valuesI18n'}
        {implode from=$I18nValues[$elementIdentifier] key=languageID item=value}'{@$languageID}': '{$value|encodeJS}'{/implode}
    {/capture}
    <script data-relocate="true">
        //<![CDATA[
        var $availableLanguages = { {implode from=$availableLanguages key=languageID item=languageName}{@$languageID}: '{$languageName}'{/implode} };
        var $values = { {@$valuesI18n} };
        $(function() {
            WCF.System.Dependency.Manager.register('Redactor_' + '{@$elementIdentifier}', function() {
                new ULTIMATE.MultipleLanguageWYSIWYG('{@$elementIdentifier}', {if $forceSelection}true{else}false{/if}, $values, $availableLanguages);
            });
        });
        //]]>
    </script>
{/if}
