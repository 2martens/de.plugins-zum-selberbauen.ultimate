<div id="pageJS" data-ajax-only="true">
	<script type="text/javascript">
		//<![CDATA[
		function initPage() {
			var actionObjects = { };
			actionObjects['de.plugins-zum-selberbauen.ultimate.content'] = { };
			actionObjects['de.plugins-zum-selberbauen.ultimate.content']['delete'] = new ULTIMATE.Action.Delete('ultimate\\data\\content\\ContentAction', '.jsContentRow');
			
			WCF.Clipboard.init('ultimate\\page\\ContentListPage', {@$hasMarkedItems}, actionObjects);
			WCF.Clipboard.reload();
			
			var options = { };
			options.emptyMessage = '{lang}wcf.acp.ultimate.content.noContents{/lang}';
			{if $pages > 1}
				options.refreshPage = true;
				{if $pages == $pageNo}
					options.updatePageNumber = -1;
				{/if}
			{else}
				options.emptyMessage = '{lang}wcf.acp.ultimate.content.noContents{/lang}';
			{/if}
			
			WCF.DOMNodeRemovedHandler.removeCallback('WCF.Table.EmptyTableHandler.jsContentRow');
			new WCF.Table.EmptyTableHandler($('#contentTableContainer'), 'jsContentRow', options);
		}
		//]]>
	</script>
</div>