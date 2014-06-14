<div id="pageJS" data-ajax-only="false">
	<script type="text/javascript">
			function initContentListPage() {
				ULTIMATE.EditSuite.Clipboard.reload();
				WCF.DOMNodeRemovedHandler.removeCallback('WCF.Table.EmptyTableHandler.jsContentRow');
			}
			
			function postInitContentListPage() {
				new ULTIMATE.Action.Delete('ultimate\\data\\content\\ContentAction', '.jsContentRow');
			}
	</script>
</div>
