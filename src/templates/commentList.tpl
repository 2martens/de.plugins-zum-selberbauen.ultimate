{foreach from=$commentList item=comment}
	<li itemprop="comment" itemscope="itemscope" itemtype="http://schema.org/UserComments" class="comment jsComment" data-comment-id="{@$comment->commentID}" data-object-type="com.woltlab.wcf.comment" data-like-liked="{if $likeData[comment][$comment->commentID]|isset}{@$likeData[comment][$comment->commentID]->liked}{/if}" data-like-likes="{if $likeData[comment][$comment->commentID]|isset}{@$likeData[comment][$comment->commentID]->likes}{else}0{/if}" data-like-dislikes="{if $likeData[comment][$comment->commentID]|isset}{@$likeData[comment][$comment->commentID]->dislikes}{else}0{/if}" data-like-users='{if $likeData[comment][$comment->commentID]|isset}{ {implode from=$likeData[comment][$comment->commentID]->getUsers() item=likeUser}"{@$likeUser->userID}": { "username": "{$likeUser->username|encodeJSON}" }{/implode} }{else}{ }{/if}' data-can-edit="{if $comment->isEditable()}true{else}false{/if}" data-can-delete="{if $comment->isDeletable()}true{else}false{/if}" data-responses="{@$comment->responses}" data-last-response-time="{@$comment->getLastResponseTime()}" data-user-id="{@$comment->userID}">
		<div class="box32">
			{if $comment->userID}
				<a href="{link controller='User' object=$comment->getUserProfile()}{/link}" title="{$comment->getUserProfile()->username}" class="framed">
					{@$comment->getUserProfile()->getAvatar()->getImageTag(32)}
				</a>
			{else}
				<span class="framed">{@$comment->getUserProfile()->getAvatar()->getImageTag(32)}</span>
			{/if}
			
			<div>
				<div class="commentContent">
					<div class="containerHeadline">
						<h3>
							{if $comment->userID}
								<div itemprop="creator" itemscope="itemscope" itemtype="http://schema.org/Person">
									<span itemprop="name"><a itemprop="url" href="{link controller='User' object=$comment->getUserProfile()}{/link}" class="userLink" data-user-id="{@$comment->userID}">{$comment->username}</a></span>
								</div>
							{else}
								<div itemprop="creator" itemscope="itemscope" itemtype="http://schema.org/Person">
									<span itemprop="name">{$comment->username}</span>
								</div>
							{/if}
							
							<small> - <span itemprop="commentTime">{@$comment->time|time}</span></small>
						</h3>
					</div>
					
					<p class="userMessage" itemprop="commentText">{@$comment->getFormattedMessage()}</p>
					
					<nav class="jsMobileNavigation buttonGroupNavigation">
						<ul class="commentOptions">
							<li class="jsReportCommentComment jsOnly" data-object-id="{@$comment->commentID}"><a title="{lang}wcf.moderation.report.reportContent{/lang}" class="jsTooltip"><span class="icon icon16 icon-warning-sign"></span> <span class="invisible">{lang}wcf.moderation.report.reportContent{/lang}</span></a></li>
							
							{event name='commentOptions'}
						</ul>
					</nav>
				</div>
				
				<ul data-responses="{@$comment->responses}" class="commentResponseList">
					{if $comment|count}
						{include file='commentResponseList' responseList=$comment}
					{/if}
				</ul>
			</div>
		</div>
	</li>
{/foreach}
