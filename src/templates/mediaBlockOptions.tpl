<div id="block_{$blockID}_tab" class="tabMenuContainer containerPadding" data-is-parent="true" data-active="block_{$blockID}_tab_type">
	<form method="post" action="">
		<nav class="tabMenu">
			<ul>
				{assign var='typeTab' value='block_'|concat:$blockID|concat:'_tab_type'}
				{assign var='embedTab' value='block_'|concat:$blockID|concat:'_tab_embed'}
				<li data-menu-item="block_{$blockID}_tab_type"><a href="{$__wcf->getAnchor($typeTab)}" title="{lang}wcf.acp.ultimate.template.mediaTab.type{/lang}">{lang}wcf.acp.ultimate.template.mediaTab.type{/lang}</a></li>
				<li data-menu-item="block_{$blockID}_tab_embed"><a href="{$__wcf->getAnchor($embedTab)}" title="{lang}wcf.acp.ultimate.template.mediaTab.embed{/lang}">{lang}wcf.acp.ultimate.template.mediaTab.embed{/lang}</a></li>
			</ul>
		</nav>
		
		<div id="block_{$blockID}_tab_type" class="container tabMenuContent containerPadding" data-parent-menu-item="block_{$blockID}_tab_type">
			<div class="info">
				<p>{lang}wcf.acp.ultimate.template.mediaTab.type.info{/lang}</p>
			</div>
			<dl class="wide">
				<dd class="inputSelect">
					<label for="mediaType_{$blockID}" class="jsTooltip" title="{lang}wcf.acp.ultimate.template.mediaTab.type.mediaType.description{/lang}">{lang}wcf.acp.ultimate.template.mediaTab.type.mediaType{/lang}</label>
					
					<select id="mediaType_{$blockID}" data-block-id="{$blockID}" data-is-block="true" name="mediaType">
						<option value="audio"{if $mediaTypeSelected == 'audio'} selected="selected"{/if}>{lang}wcf.acp.ultimate.template.mediaTab.type.mediaType.audio{/lang}</option>
						<option value="photo"{if $mediaTypeSelected == 'photo'} selected="selected"{/if}>{lang}wcf.acp.ultimate.template.mediaTab.type.mediaType.photo{/lang}</option>
						<option value="video"{if $mediaTypeSelected == 'video'} selected="selected"{/if}>{lang}wcf.acp.ultimate.template.mediaTab.type.mediaType.video{/lang}</option>
					</select>
				</dd>
			</dl>
		</div>
		<div id="block_{$blockID}_tab_embed" class="container tabMenuContent containerPadding" data-parent-menu-item="block_{$blockID}_tab_embed">
			<dl class="wide">
				<dd class="inputText">
					<label for="mediaSource_{$blockID}" class="jsTooltip" title="{lang}wcf.acp.ultimate.template.mediaTab.embed.embedURL.description{/lang}">{lang}wcf.acp.ultimate.template.mediaTab.embed.embedURL{/lang}</label>
					<input type="url" id="mediaSource_{$blockID}" name="mediaSource" data-block-id="{$blockID}" data-is-block="true" value="{@$mediaSource}" />
				</dd>
				<dd class="inputSelect">
					<label for="mimeType_{$blockID}" class="jsTooltip" title="{lang}wcf.acp.ultimate.template.mediaTab.embed.mimeType.description{/lang}">{lang}wcf.acp.ultimate.template.mediaTab.embed.mimeType{/lang}</label>
					
					<select id="mimeType_{$blockID}" data-block-id="{$blockID}" data-is-block="true" name="mimeType">
					{foreach from=$mimeTypes key=mime item=mimetype}
						<option value="{@$mime}"{if $mimeTypeSelected == $mime} selected="selected"{/if}>{@$mimetype}</option>
					{/foreach}
					</select>
				</dd>
				<dd class="inputText">
					<label for="mediaHeight_{$blockID}" class="jsTooltip" title="{lang}wcf.acp.ultimate.template.mediaTab.embed.mediaHeight.description{/lang}">{lang}wcf.acp.ultimate.template.mediaTab.embed.mediaHeight{/lang}</label>
					<input type="number" id="mediaHeight_{$blockID}" name="mediaHeight" data-block-id="{$blockID}" data-is-block="true" value="{@$mediaHeight}" />
				</dd>
				<dd class="inputText">
					<label for="mediaWidth_{$blockID}" class="jsTooltip" title="{lang}wcf.acp.ultimate.template.mediaTab.embed.mediaWidth.description{/lang}">{lang}wcf.acp.ultimate.template.mediaTab.embed.mediaWidth{/lang}</label>
					<input type="number" id="mediaWidth_{$blockID}" name="mediaWidth" data-block-id="{$blockID}" data-is-block="true" value="{@$mediaWidth}" />
				</dd>
				<dd class="inputSelect">
					<label for="alignment_{$blockID}" class="jsTooltip" title="{lang}wcf.acp.ultimate.template.mediaTab.embed.alignment.description{/lang}">{lang}wcf.acp.ultimate.template.mediaTab.embed.alignment{/lang}</label>
					
					<select id="alignment_{$blockID}" data-block-id="{$blockID}" data-is-block="true" name="alignment">
						<option value="left"{if $alignmentSelected == 'left'} selected="selected"{/if}>{lang}wcf.acp.ultimate.template.mediaTab.embed.alignment.left{/lang}</option>
						<option value="center"{if $alignmentSelected == 'center'} selected="selected"{/if}>{lang}wcf.acp.ultimate.template.mediaTab.embed.alignment.center{/lang}</option>
						<option value="right"{if $alignmentSelected == 'right'} selected="selected"{/if}>{lang}wcf.acp.ultimate.template.mediaTab.embed.alignment.right{/lang}</option>
					</select>
				</dd>
			</dl>
		</div>
		<div class="formSubmit">
			<input type="submit" id="blockSubmitButton" name="submitButton" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		</div>
	</form>
</div>