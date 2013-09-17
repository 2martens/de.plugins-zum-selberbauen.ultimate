{hascontent}
	<ul class="sidebarBoxList">
		{content}
			{foreach from=$links key=linkID item=link}
				<li class="box24">
					{@$link->getAnchorTag()}
				</li>
			{/foreach}
		{/content}
	</ul>
{/hascontent}