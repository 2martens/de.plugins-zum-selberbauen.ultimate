{if $customMenu|isset}
<script data-relocate="true" type="text/javascript">
//<![CDATA[
	$(function() {
		$('#mainMenu').replaceWith(customMenu);
		$('.navigation.navigationHeader > .navigationMenuItems').replaceWith(customMenuSubMenu);
	});
//]]>
</script>
{/if}