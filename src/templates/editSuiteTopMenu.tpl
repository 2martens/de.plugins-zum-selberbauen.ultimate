{if $__wcf->user->userID && $__wcf->session->getPermission('user.ultimate.editing.canAccessEditSuite')}
    <li id="editSuiteTopMenu">
        <a href="{link controller='EditSuite' application='ultimate'}{/link}">
            <span class="icon icon16 icon-pencil"></span>
            <span>{lang}ultimate.edit.suite{/lang}</span>
        </a>
    </li>
{/if}
