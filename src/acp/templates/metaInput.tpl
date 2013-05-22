<dl{if $errorField == 'metaDescription"} class="formError"{/if}>
	<dt><label for="metaDescription">{lang}wcf.acp.ultimate.metaDescription</label></dt>
	<dd>
		<input type="text" id="metaDescription" name="metaDescription" value="{@$metaDescription}" class="long" maxlength="255" />
		{if $errorField == 'metaDescription'}
			<small class="innerError">
				{lang}wcf.acp.ultimate.metaDescription.error.{@$errorType}{/lang}
			</small>
		{/if}
	</dd>
</dl>
<dl{if $errorField == 'metaKeywords"} class="formError"{/if}>
	<dt><label for="metaKeywords">{lang}wcf.acp.ultimate.metaKeywords</label></dt>
	<dd>
		<input type="text" id="metaKeywords" name="metaKeywords" value="{@$metaKeywords}" class="long" maxlength="255" />
		{if $errorField == 'metaKeywords'}
			<small class="innerError">
				{lang}wcf.acp.ultimate.metaKeywords.error.{@$errorType}{/lang}
			</small>
		{/if}
	</dd>
</dl>