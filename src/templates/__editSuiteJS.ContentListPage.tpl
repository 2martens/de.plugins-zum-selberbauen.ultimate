<div id="pageJS" data-ajax-only="false">
	<script type="text/javascript">
			initFunction = function initContentListPage() {
				ULTIMATE.EditSuite.Clipboard.reload();
				WCF.DOMNodeRemovedHandler.removeCallback('WCF.Table.EmptyTableHandler.jsContentRow');
			};
			
			postInitFunction = function postInitContentListPage() {
				new ULTIMATE.Action.Delete('ultimate\\data\\content\\ContentAction', '.jsContentRow');
			};
	</script>
</div>
