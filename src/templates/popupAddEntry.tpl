<div id="popupAddEntry" class="" style="display: none;">
    <form method="post" onsubmit="return validate()" action="javascript:addEntry()">
        <dl>
            <dt><label for="componentID">{lang}ultimate.template.addEntry.componentID{/lang}</label></dt>
            <dd>
                <select id="componentID" name="componentID" size="1">
                    <option value="0">{lang}ultimate.template.addEntry.componentID.select{/lang}</option>
                    {foreach from=$components item=$component}
                    <option value="{$component->componentID}">{$component->componentTitle}</option>
                    {/foreach}
                </select>
            </dd>
        </dl>
        <dl>
            <dt><label for="contentID">{lang}ultimate.template.addEntry.contentID{/lang}</label></dt>
            <dd>
                <select id="contentID" name="contentID" size="1">
                    <option value="0">{lang}ultimate.template.addEntry.contentID.select{/lang}</option>
                    {foreach from=$contents item=$content}
                    <option value="{$content->contentID}">{$content->contentTitle}</option>
                    {/foreach}
                </select>
            </dd>
        </dl>
        
        <div class="formSubmit">
            <input type="reset" value="{lang}wcf.global.button.reset{/lang}" accesskey="r" />
            <input type="submit" id="loginSubmitButton" name="submitButton" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
            <input type="hidden" name="url" value="/~Jim-Standard/woltlab/wbb4/ultimate/index.php" />
        </div>
    </form>
</div>
<script type="text/javascript">
    /* <[CDATA[ */
    function validate() {
        if (document.getElementById('componentID').value == '0') {
            alert('{lang}ultimate.template.addEntry.componentID.error.notSelected{/lang}');
            document.getElementById('componentID').focus();
            return false;
        }
        if (document.getElementById('contentID').value == '0') {
            alert('{lang}ultimate.template.addEntry.contentID.error.notSelected{/lang}');
            document.getElementById('contentID').focus();
            return false;
        }
        return true;
    }
    /* ]]> */
</script>