<div id="pageJS" data-ajax-only="false">
    <script type="text/javascript">
        function initContentVersionListPage() {
            ULTIMATE.EditSuite.Clipboard.reload();
            WCF.DOMNodeRemovedHandler.removeCallback('WCF.Table.EmptyTableHandler.jsContentVersionRow');
        }

        function postInitContentVersionListPage() {
            new ULTIMATE.Action.Delete('ultimate\\data\\content\\version\\ContentVersionAction', '.jsContentVersionRow');
        }
    </script>
</div>
