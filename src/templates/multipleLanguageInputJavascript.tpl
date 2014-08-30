{if $availableLanguages|count > 1}
	<script data-relocate="true">
		//<![CDATA[
        var $availableLanguages = { {implode from=$availableLanguages key=languageID item=languageName}{@$languageID}: '{$languageName}'{/implode} };
        var $values = { {implode from=$I18nValues[$elementIdentifier] key=languageID item=value}'{@$languageID}': '{$value}'{/implode} };
        $(function() {
			new WCF.MultipleLanguageInput('{@$elementIdentifier}', {if $forceSelection}true{else}false{/if}, $values, $availableLanguages);
		});
		//]]>
	</script>
{/if}
