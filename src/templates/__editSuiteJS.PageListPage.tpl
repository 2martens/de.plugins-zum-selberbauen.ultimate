<div id="pageJS" data-ajax-only="false">
	<script type="text/javascript">
			initFunction = function initPageListPage() {
				ULTIMATE.EditSuite.Clipboard.reload();
				WCF.DOMNodeRemovedHandler.removeCallback('WCF.Table.EmptyTableHandler.jsContentRow');
			};
			
			postInitFunction = function postInitPageListPage() {
				new ULTIMATE.Action.Delete('ultimate\\data\\page\\PageAction', '.jsContentRow');
			};
	</script>
</div>
