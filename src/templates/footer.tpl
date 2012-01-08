				<div id="breadcrumbsFooter">{include file='breadcrumbs' sandbox=false}</div>
				
			</section>
			<!-- /CONTENT -->
		</div>
	</div>
	<!-- /MAIN -->
	
	<!-- FOOTER -->
	<footer id="pageFooter" class="pageFooter">
		<div>
			{include file='footerMenu'}
		</div>
		
		{if ENABLE_BENCHMARK}{include file='benchmark'}{/if}
		
		{event name='copyright'}
	</footer>
	<!-- /FOOTER -->
	<a id="bottom"></a>
	