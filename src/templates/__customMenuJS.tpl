{if $customMenuAssigned|isset}
<script data-relocate="true" type="text/javascript">
//<![CDATA[
	$(function() {
		$('#mainMenu').replaceWith('{@$customMenuJS}');
		$('.navigation.navigationHeader > .navigationMenuItems').replaceWith('{@$customMenuSubMenuJS}');
		WCF.System.FlexibleMenu.registerMenu('mainMenu');
		WCF.System.FlexibleMenu.registerMenu($('.navigationHeader:eq(0)').wcfIdentify());
	});
//]]>
</script>
{/if}
