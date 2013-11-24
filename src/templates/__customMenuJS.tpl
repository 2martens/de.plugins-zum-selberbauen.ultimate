{if $customMenu|isset}
<script data-relocate="true" type="text/javascript">
//<![CDATA[
	$(function() {
		$('#mainMenu').replaceWith(customMenu);
		$('.navigation.navigationHeader > .navigationMenuItems').replaceWith(customMenuSubMenu);
		WCF.System.FlexibleMenu.registerMenu('mainMenu');
		WCF.System.FlexibleMenu.registerMenu($('.navigationHeader:eq(0)').wcfIdentify());
	});
//]]>
</script>
{/if}