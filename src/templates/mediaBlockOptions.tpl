<div id="block-{$blockID}-tab" class="tabMenuContainer tabMenuContent containerPadding panel" data-store="block-{$blockID}-tab-type">
	<form method="post" action="">
	<nav class="tabMenu subTabsContainer">
		<ul class="subTabs">
			<li><a href="#block-{$blockID}-tab-type" title="{lang}wcf.acp.ultimate.template.mediaTab.type{/lang}">{lang}wcf.acp.ultimate.template.mediaTab.type{/lang}</a></li>
			<li><a href="#block-{$blockID}-tab-embed" title="{lang}wcf.acp.ultimate.template.mediaTab.embed{/lang}">{lang}wcf.acp.ultimate.template.mediaTab.embed{/lang}</a></li>
		</ul>
	</nav>
	
	<div class="subTabsContentContainer">
		<div id="block-{$blockID}-tab-type" class="tabMenuContent subTabsContent containerPadding">
			<div class="info">
				<p>{lang}wcf.acp.ultimate.template.mediaTab.type.info{/lang}</p>
			</div>
			<dl class="wide">
				<dd class="inputSelect">
					<label for="mediaType-{$blockID}" class="jsTooltip" title="{lang}wcf.acp.ultimate.template.mediaTab.type.mediaType.description{/lang}">{lang}wcf.acp.ultimate.template.mediaTab.type.mediaType{/lang}</label>
					
					<select id="mediaType-{$blockID}" data-block-id="{$blockID}" data-is-block="true" name="mediaType">
						<option value="audio"{if $mediaTypeSelected == 'audio'} selected="selected"{/if}>{lang}wcf.acp.ultimate.template.mediaTab.type.mediaType.audio{/lang}</option>
						<option value="photo"{if $mediaTypeSelected == 'photo'} selected="selected"{/if}>{lang}wcf.acp.ultimate.template.mediaTab.type.mediaType.photo{/lang}</option>
						<option value="video"{if $mediaTypeSelected == 'video'} selected="selected"{/if}>{lang}wcf.acp.ultimate.template.mediaTab.type.mediaType.video{/lang}</option>
					</select>
				</dd>
			</dl>
		</div>
		<div id="block-{$blockID}-tab-embed" class="tabMenuContent subTabsContent containerPadding">
			<dl class="wide">
				<dd class="inputText">
					<label for="embedURL-{$blockID}" class="jsTooltip" title="{lang}wcf.acp.ultimate.template.mediaTab.embed.embedURL.description{/lang}">{lang}wcf.acp.ultimate.template.mediaTab.embed.embedURL{/lang}</label>
					<input type="url" id="embedURL-{$blockID}" name="mediaSource" data-block-id="{$blockID}" data-is-block="true" value="{@$mediaSource}" />
				</dd>
				<dd class="inputSelect">
					<label for="mimeType-{$blockID}" class="jsTooltip" title="{lang}wcf.acp.ultimate.template.mediaTab.embed.mimeType.description{/lang}">{lang}wcf.acp.ultimate.template.mediaTab.embed.mimeType{/lang}</label>
					
					<select id="mimeType-{$blockID}" data-block-id="{$blockID}" data-is-block="true" name="mimeType">
					{foreach from=$mimetypes key=mime item=mimetype}
						<option value="{@$mime}"{if $mimeTypeSelected == $mime} selected="selected"{/if}>{@$mime}</option>
					{/foreach}
					</select>
				</dd>
				<dd class="inputText">
					<label for="mediaHeight-{$blockID}" class="jsTooltip" title="{lang}wcf.acp.ultimate.template.mediaTab.embed.mediaHeight.description{/lang}">{lang}wcf.acp.ultimate.template.mediaTab.embed.mediaHeight{/lang}</label>
					<input type="number" id="mediaHeight-{$blockID}" name="mediaHeight" data-block-id="{$blockID}" data-is-block="true" value="{@$mediaHeight}" />
				</dd>
				<dd class="inputText">
					<label for="mediaWidth-{$blockID}" class="jsTooltip" title="{lang}wcf.acp.ultimate.template.mediaTab.embed.mediaWidth.description{/lang}">{lang}wcf.acp.ultimate.template.mediaTab.embed.mediaWidth{/lang}</label>
					<input type="number" id="mediaWidth-{$blockID}" name="mediaWidth" data-block-id="{$blockID}" data-is-block="true" value="{@$mediaWidth}" />
				</dd>
			</dl>
		</div>
	</div>
	<div class="formSubmit">
		<input type="submit" id="blockSubmitButton" name="submitButton" value="{lang}wcf.global.submit{/lang}" accesskey="s" />
	</div>
	</form>
</div>