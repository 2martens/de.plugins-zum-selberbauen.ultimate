<div class="marginTop tabularBox tabularBoxTitle messageGroupList">
	<header>
		<h2>{lang}ultimate.content.contents{/lang}</h2>
	</header>
	
	<table class="table">
		<thead>
			<tr>
				<th class="columnTitle columnSubject">{lang}ultimate.content.contentTitle{/lang}</th>
				{if MODULE_LIKE && $__wcf->getSession()->getPermission('user.like.canViewLike')}<th class="columnDigits columnLikes">{lang}wcf.like.cumulativeLikes{/lang}</th>{/if}
				{* TODO views
				<th class="columnDigits columnViews">{lang}ultimate.content.views{/lang}</th>*}
				
				{event name='columnHeads'}
			</tr>
		</thead>
		
		<tbody>
			{include file='contentList' application='ultimate'}
		</tbody>
	</table>
</div>