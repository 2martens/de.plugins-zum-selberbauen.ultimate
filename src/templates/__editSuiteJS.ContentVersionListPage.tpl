<div id="pageJS" data-ajax-only="false">
    <script type="text/javascript">
        initFunction = function initContentVersionListPage() {
            ULTIMATE.EditSuite.Clipboard.reload();
            WCF.DOMNodeRemovedHandler.removeCallback('WCF.Table.EmptyTableHandler.jsContentVersionRow');
        };

        postInitFunction = function postInitContentVersionListPage() {
            new ULTIMATE.Action.Delete('ultimate\\data\\content\\version\\ContentVersionAction', '.jsContentVersionRow');
        };
    </script>
</div>
