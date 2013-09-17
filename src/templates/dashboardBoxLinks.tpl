{hascontent}
	<ul class="sidebarBoxList">
		{content}
			{foreach from=$links key=linkID item=link}
				<li class="box24">
					<a href="{@$link->linkURL}">{@$link->linkName}</a>
				</li>
			{/foreach}
		{/content}
	</ul>
{/hascontent}