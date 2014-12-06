<div id="pageJS" data-ajax-only="false">
	<script type="text/javascript">
			function initPageListPage() {
				ULTIMATE.EditSuite.Clipboard.reload();
				WCF.DOMNodeRemovedHandler.removeCallback('WCF.Table.EmptyTableHandler.jsContentRow');
			}
			
			function postInitPageListPage() {
				new ULTIMATE.Action.Delete('ultimate\\data\\page\\PageAction', '.jsContentRow');
			}
	</script>
</div>
