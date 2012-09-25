{include file="documentHeader"}
<head>
	<title>VisualEditor - {lang}{PAGE_TITLE}{/lang}</title>
	
	{include file='headInclude'}
	{@$__wcf->getUltimateStyleHandler()->getVisualEditorStylesheet()}
	<script type="text/javascript">
	/* <![CDATA[ */
	$(function() {
		WCF.Language.addObject({
			'ultimate.visualEditor.layoutSelector.show': '{lang}ultimate.visualEditor.layoutSelector.show{/lang}',
			'ultimate.visualEditor.layoutSelector.hide': '{lang}ultimate.visualEditor.layoutSelector.hide{/lang}',
			'ultimate.visualEditor.layoutSelector.button.edit': '{lang}ultimate.visualEditor.layoutSelector.button.edit{/lang}',
			'ultimate.visualEditor.layoutSelector.button.deleteTemplate': '{lang}ultimate.visualEditor.layoutSelector.button.deleteTemplate{/lang}',
			'ultimate.visualEditor.layoutSelector.button.deleteTemplate.sure': '{lang}ultimate.visualEditor.layoutSelector.button.deleteTemplate.sure{/lang}',
			'ultimate.visualEditor.layoutSelector.status.currentlyEditing': '{lang}ultimate.visualEditor.layoutSelector.status.currentlyEditing{/lang}',
			'ultimate.visualEditor.block.idTooltip': '{lang}ultimate.visualEditor.idTooltip{/lang}',
			'ultimate.visualEditor.block.changeBlockTypeTooltip': '{lang}ultimate.visualEditor.block.changeBlockTypeTooltip{/lang}',
			'ultimate.visualEditor.block.optionsTooltip': '{lang}ultimate.visualEditor.block.optionsTooltip{/lang}',
			'ultimate.visualEditor.block.delete': '{lang}ultimate.visualEditor.block.delete{/lang}',
			'ultimate.visualEditor.block.delete.sure': '{lang}ultimate.visualEditor.block.blockTypeUnknown{/lang}'
		});
		ULTIMATE.Permission.addObject({
			'admin.content.ultimate.canDeleteTemplate': {if $__wcf->session->getPermission('admin.content.ultimate.canDeleteTemplate')}true{else}false{/if}
		});
		WCF.TabMenu.init();
		var allBlockTypes = $.parseJSON({@$blockTypesJSON});
		
		new ULTIMATE.VisualEditor('blockTypePopup', allBlockTypes);
		
		{if $__wcf->session->getPermission('admin.content.ultimate.canDeleteTemplate')}
			new WCF.Action.Delete('ultimate\\data\\template\\TemplateAction', $('.jsLayoutItem'));
		{/if}
	});
	/* ]]> */
	</script>
</head>

<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' visualEditor=true skipBreadcrumbs='yes'}
			{* implement iFrame, panels, etc. *}
			<div id="loading">
				<div class="loading-message">
					<div class="logo"></div>
				
					<div class="loading-bar">
						<div class="loading-bar-inside" style="width: 123.5px; "></div>
					</div>
		
					<p class="tip"></p>
				</div>
			</div>
			<form id="visualEditorForm" method="post" action="{link controller='VisualEditor'}{/link}">
			
				<iframe src="{link controller='VisualEditor'}visualEditorIFrame=true{/link}" class="content"></iframe>
				<div id="iframeOverlay"></div>
				<div id="iframeLoadingOverlay"></div>
				<div id="hiddenInputs"></div>
				<div id="layoutSelectorOffset" class="open">
					<div id="layoutSelectorContainer">
						<span id="layoutSelectorToggle">{lang}ultimate.visualEditor.layoutSelector.hide{/lang}</span>
						<div id="layoutSelector" class="tabMenuContainer" data-store="layoutSelectorTemplatesContainer">
							<nav id="layoutSelectorTabs" class="tabMenu">
								<ul>
									<li><a href="#layoutSelectorPagesContainer" title="{lang}ultimate.visualEditor.layoutSelector.pages{/lang}">{lang}ultimate.visualEditor.layoutSelector.pages{/lang}</a></li>
									<li><a href="#layoutSelectorTemplatesContainer" title="{lang}ultimate.visualEditor.layoutSelector.templates{/lang}">{lang}ultimate.visualEditor.layoutSelector.templates{/lang}</a></li>
								</ul>
							</nav>
							<div id="layoutSelectorPagesContainer" class="tabMenuContent">
							<div id="layoutSelectorPages" class="layoutSelectorContent">
								<ul>
									<li class="layoutItem">
										<span class="layout layoutPage" data-layout-id="index">
											<strong>{lang}ultimate.visualEditor.layoutSelector.pages.index{/lang}</strong>
											<span class="status statusTemplate" data-template-id="none">
											</span>
											<span class="status statusCurrentlyEditing">
												
												{lang}ultimate.visualEditor.layoutSelector.status.currentlyEditing{/lang}
											</span>
											<span class="removeTemplate button ultimateHidden">
												
												{lang}ultimate.visualEditor.layoutSelector.button.removeTemplate{/lang}
											</span>
											<span class="edit button ultimateHidden">
												
												{lang}ultimate.visualEditor.layoutSelector.button.edit{/lang}
											</span>
										</span>
									</li>
									<li class="layoutItem hasChildren">
										<a class="collapsibleButton jsCollapsible jsTooltip" isOpen="0" data-collapsible-container="collapsibleSingle" title="{lang}wcf.global.button.collapsible{/lang}"><img src="{@$__wcf->getPath()}icon/closed.svg" alt="" class="icon16" /></a>
										<span class="layout layoutPage" data-layout-id="single">
											<strong>{lang}ultimate.visualEditor.layoutSelector.pages.single{/lang}</strong>
											<span class="status statusTemplate" data-template-id="none">
											</span>
											<span class="status statusCurrentlyEditing">
											
												{lang}ultimate.visualEditor.layoutSelector.status.currentlyEditing{/lang}
											</span>
											<span class="removeTemplate button ultimateHidden">
											
												{lang}ultimate.visualEditor.layoutSelector.button.removeTemplate{/lang}
											</span>
											<span class="edit button ultimateHidden">
												
												{lang}ultimate.visualEditor.layoutSelector.button.edit{/lang}
											</span>
										</span>
										<ul id="collapsibleSingle">
											<li class="layoutItem">
												<a class="collapsibleButton jsCollapsible jsTooltip" isOpen="0" data-collapsible-container="collapsibleSingleContent" title="{lang}wcf.global.button.collapsible{/lang}"><img src="{@$__wcf->getPath()}icon/closed.svg" alt="" class="icon16" /></a>
												<span class="layout layoutPage" data-layout-id="singleContent">
													<strong>{lang}ultimate.visualEditor.layoutSelector.pages.single.content{/lang}</strong>
													<span class="status statusTemplate" data-template-id="none">
													</span>
													<span class="status statusCurrentlyEditing">
														
														{lang}ultimate.visualEditor.layoutSelector.status.currentlyEditing{/lang}
													</span>
													<span class="removeTemplate button ultimateHidden">
													
														{lang}ultimate.visualEditor.layoutSelector.button.removeTemplate{/lang}
													</span>
													<span class="edit button ultimateHidden">
													
														{lang}ultimate.visualEditor.layoutSelector.button.edit{/lang}
													</span>
												</span>
												<ul id="collapsibleSingleContent">
													{foreach from=$contents key=contentID item=content}
														<li class="layoutItem">
															<span class="layout layoutPage" data-layout-id="singleContent-{$contentID}">
																<strong>{lang}{$content->contentTitle}{/lang}</strong>
																<span class="status statusTemplate" data-template-id="none">
																</span>
																<span class="status statusCurrentlyEditing">
														
																	{lang}ultimate.visualEditor.layoutSelector.status.currentlyEditing{/lang}
																</span>
																<span class="removeTemplate button ultimateHidden">
													
																	{lang}ultimate.visualEditor.layoutSelector.button.removeTemplate{/lang}
																</span>
																<span class="edit button ultimateHidden">
													
																	{lang}ultimate.visualEditor.layoutSelector.button.edit{/lang}
																</span>
															</span>
														</li>
													{/foreach}
												</ul>
											</li>
											<li class="layoutItem">
												<a class="collapsibleButton jsCollapsible jsTooltip" isOpen="0" data-collapsible-container="collapsibleSinglePage" title="{lang}wcf.global.button.collapsible{/lang}"><img src="{@$__wcf->getPath()}icon/closed.svg" alt="" class="icon16" /></a>
												<span class="layout layoutPage" data-layout-id="singlePage">
													<strong>{lang}ultimate.visualEditor.layoutSelector.pages.single.page{/lang}</strong>
													<span class="status statusTemplate" data-template-id="none">
													</span>
													<span class="status statusCurrentlyEditing">
														
														{lang}ultimate.visualEditor.layoutSelector.status.currentlyEditing{/lang}
													</span>
													<span class="removeTemplate button ultimateHidden">
													
														{lang}ultimate.visualEditor.layoutSelector.button.removeTemplate{/lang}
													</span>
													<span class="edit button ultimateHidden">
													
														{lang}ultimate.visualEditor.layoutSelector.button.edit{/lang}
													</span>
												</span>
												<ul id="collapsibleSinglePage">
													{foreach from=$pages key=pageID item=page}
														<li class="layoutItem">
															<span class="layout layoutPage" data-layout-id="singlePage-{$pageID}">
																<strong>{lang}{$page->pageTitle}{/lang}</strong>
																<span class="status statusTemplate" data-template-id="none">
																</span>
																<span class="status statusCurrentlyEditing">
														
																	{lang}ultimate.visualEditor.layoutSelector.status.currentlyEditing{/lang}
																</span>
																<span class="removeTemplate button ultimateHidden">
													
																	{lang}ultimate.visualEditor.layoutSelector.button.removeTemplate{/lang}
																</span>
																<span class="edit button ultimateHidden">
													
																	{lang}ultimate.visualEditor.layoutSelector.button.edit{/lang}
																</span>
															</span>
														</li>
													{/foreach}
												</ul>
											</li>
										</ul>
									</li>
									<li class="layoutItem hasChildren">
										<a class="collapsibleButton jsCollapsible jsTooltip" isOpen="0" data-collapsible-container="collapsibleArchive" title="{lang}wcf.global.button.collapsible{/lang}"><img src="{@$__wcf->getPath()}icon/closed.svg" alt="" class="icon16" /></a>
										<span class="layout layoutPage" data-layout-id="archive">
											<strong>{lang}ultimate.visualEditor.layoutSelector.pages.archive{/lang}</strong>
											<span class="status statusTemplate" data-template-id="none">
											</span>
											<span class="status statusCurrentlyEditing">
											
												{lang}ultimate.visualEditor.layoutSelector.status.currentlyEditing{/lang}
											</span>
											<span class="removeTemplate button ultimateHidden">
											
												{lang}ultimate.visualEditor.layoutSelector.button.removeTemplate{/lang}
											</span>
											<span class="edit button ultimateHidden">
												
												{lang}ultimate.visualEditor.layoutSelector.button.edit{/lang}
											</span>
										</span>
										<ul id="collapsibleArchive">
											<li class="layoutItem">
												<a class="collapsibleButton jsCollapsible jsTooltip" isOpen="0" data-collapsible-container="collapsibleArchiveCategory" title="{lang}wcf.global.button.collapsible{/lang}"><img src="{@$__wcf->getPath()}icon/closed.svg" alt="" class="icon16" /></a>
												<span class="layout layoutPage" data-layout-id="archiveCategory">
													<strong>{lang}ultimate.visualEditor.layoutSelector.pages.archive.category{/lang}</strong>
													<span class="status statusTemplate" data-template-id="none">
													</span>
													<span class="status statusCurrentlyEditing">
														
														{lang}ultimate.visualEditor.layoutSelector.status.currentlyEditing{/lang}
													</span>
													<span class="removeTemplate button ultimateHidden">
													
														{lang}ultimate.visualEditor.layoutSelector.button.removeTemplate{/lang}
													</span>
													<span class="edit button ultimateHidden">
													
														{lang}ultimate.visualEditor.layoutSelector.button.edit{/lang}
													</span>
												</span>
												<ul id="collapsibleArchiveCategory">
													{foreach from=$categories key=categoryID item=category}
														<li class="layoutItem">
															<span class="layout layoutPage" data-layout-id="archiveCategory-{$categoryID}">
																<strong>{lang}{$category->categoryTitle}{/lang}</strong>
																<span class="status statusTemplate" data-template-id="none">
																</span>
																<span class="status statusCurrentlyEditing">
														
																	{lang}ultimate.visualEditor.layoutSelector.status.currentlyEditing{/lang}
																</span>
																<span class="removeTemplate button ultimateHidden">
													
																	{lang}ultimate.visualEditor.layoutSelector.button.removeTemplate{/lang}
																</span>
																<span class="edit button ultimateHidden">
													
																	{lang}ultimate.visualEditor.layoutSelector.button.edit{/lang}
																</span>
															</span>
														</li>
													{/foreach}
												</ul>
											</li>
										</ul>
									</li>
								</ul>
							</div>
							</div>
							<div id="layoutSelectorTemplatesContainer" class="tabMenuContent">
							<div id="layoutSelectorTemplates" class="layoutSelectorContent">
								<ul>
									{foreach from=$templates key=templateID item=template}
										<li class="layoutItem jsLayoutItem" data-object-id="{$templateID}" data-object-name="{$template->getTitle()}">
											<span class="layout layoutTemplate" data-layout-id="template-{$templateID}">
												<strong class="templateName">{$template->getTitle()}</strong>
												
												{if $__wcf->session->getPermission('admin.content.ultimate.canDeleteTemplate')}
													<img src="{@$__wcf->getPath()}icon/delete.svg" alt="" title="{lang}wcf.global.button.delete{/lang}" class="icon16 jsDeleteButton jsTooltip" data-object-id="{@$templateID}" data-confirm-message="{lang}ultimate.visualEditor.layoutSelector.button.deleteTemplate.sure{/lang}" />
												{else}
													<img src="{@$__wcf->getPath()}icon/delete.svg" alt="" title="{lang}wcf.global.button.delete{/lang}" class="icon16 disabled" />
												{/if}
												<span class="status statusCurrentlyEditing">
													
													{lang}ultimate.visualEditor.layoutSelector.status.currentlyEditing{/lang}
												</span>												
												<span class="edit button">
													
													{lang}ultimate.visualEditor.layoutSelector.button.edit{/lang}
												</span>
											</span>
										</li>
									{/foreach}
								</ul>
							</div>
							<div id="templateNameInputContainer">
								<dl>
									<dd>
										<label for="templateName" style="display: none;">{lang}ultimate.visualEditor.layoutSelector.templates.templateName{/lang}</label>
										<input type="text" name="templateName" id="templateName" value="" placeholder="{lang}ultimate.visualEditor.layoutSelector.templates.templateName.placeholder{/lang}" />
										<span class="button addTemplate">{lang}ultimate.visualEditor.layoutSelector.button.addTemplate{/lang}</span>
									</dd>
								</dl>
							</div>
							</div>
							
						</div>
					</div>
				</div>
				<div id="bottomPanel" class="tabMenuContainer" data-store="setupTab">
					<nav id="bottomPanelTabs" class="tabMenu">
						<ul>
							{* doesn't used anymore
							<li><a href="#setupTab" title="{lang}ultimate.visualEditor.setupTab{/lang}">{lang}ultimate.visualEditor.setupTab{/lang}</a></li>
							*}
							<li id="minimize"><span class="jsTooltip" title="{lang}ultimate.visualEditor.bottomPanel.minimize{/lang}"><img src="{@$__wcf->getPath()}icon/remove.svg" alt="" class="icon32" /></span></li>
						</ul>
					</nav>
					{*include file='setupTab'*}
				
				</div>
				<div id="blockTypePopup" class="ultimateHidden">
					<h4 id="blockTypePopupHeading">{lang}ultimate.visualEditor.selectBlockType{/lang}</h4>
					<ul>
						{foreach from=$blockTypes key=blockTypeName item=blockType}
							<li class="jsTooltip" id="block-type-{$blockType->cssIdentifier}" title="{lang}{$blockType->blockTypeTooltip}{/lang}">{lang}{$blockType->blockTypeName}{/lang}</li>
						{/foreach}
					</ul>
				</div>
			</form>
		</section>
		<!-- /CONTENT -->
	</div>
</div>
<!-- /MAIN -->

</body>
</html>