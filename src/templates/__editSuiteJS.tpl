{*if JQUERY_SOURCE == 'google'}
	<script data-relocate="true" src="http://code.jquery.com/mobile/1.4.2/jquery.mobile-1.4.2.min.js"></script>
{elseif JQUERY_SOURCE == 'microsoft'}
	<script data-relocate="true" src="//ajax.aspnetcdn.com/ajax/jquery.mobile/1.4.2/jquery.mobile-1.4.2.min.js"></script> 
{elseif JQUERY_SOURCE == 'cloudflare'}
	<script data-relocate="true" src="//cdnjs.cloudflare.com/ajax/libs/jquery.mobile/1.4.2/jquery.mobile.min.js"></script>
{else}
	<script data-relocate="true" type="text/javascript" src="{@$__wcf->getPath()}js/3rdParty/jquery.mobile-1.4.2{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@$__wcfVersion}"></script>
{/if*}
<script data-relocate="true" type="text/javascript" src="{@$__wcf->getPath()}js/WCF.Tagging{if !ENABLE_DEBUG_MODE}.min{/if}.js"></script>
<script data-relocate="true" type="text/javascript" src="{@$__wcf->getPath('ultimate')}js/ULTIMATE{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@$__wcfVersion}"></script>
<script data-relocate="true" type="text/javascript" src="{@$__wcf->getPath('ultimate')}js/ULTIMATE.EditSuite{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@$__wcfVersion}"></script>
<script data-relocate="true" type="text/javascript" src="{@$__wcf->getPath('ultimate')}js/ULTIMATE.Tagging{if !ENABLE_DEBUG_MODE}.min{/if}.js"></script>