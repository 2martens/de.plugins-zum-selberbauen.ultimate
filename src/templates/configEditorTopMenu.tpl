<!-- configEditor link -->
<li id="configEditorTopMenuBox">
    <span class="dropdownCaption"><a id="configEditorLink" href="{link controller='ConfigEditor'}{/link}">{lang}ultimate.template.topMenu.configEditor{/lang}</a></span>
    <div id="configEditorPopup" class="ultimateHidden">
        <form id="configEditorPopupForm" method="post" action="{link controller='ConfigEditor'}{/link}">
            <dl>
                <dt>{lang}ultimate.template.topMenu.action{/lang}</dt>
                <dd><label><input type="radio" name="action" value="add" checked="checked" />{lang}ultimate.template.topMenu.action.add{/lang}</label></dd>
                <dd><label><input type="radio" name="action" value="edit" />{lang}ultimate.template.topMenu.action.edit{/lang}</label></dd>
            </dl>
            
            <dl class="disabled">
                <dt><label for="configID">{lang}ultimate.template.topMenu.configID{/lang}</label></dt>
                <dd>
                    <select size="1" id="configID" name="configID" class="disabled">
                        <option value="0">{lang}ultimate.template.topMenu.configID.select{/lang}</option>
                        {foreach from=$configs item=$config}
                        <option value="{$config->configID}"{if $config->configID == $configID} selected="selected"{/if}>{@$config->configTitle}</option>
                        {/foreach}
                    </select>
                </dd>
            </dl>
            
            <div class="formSubmit">
                <input type="reset" value="{lang}wcf.global.button.reset{/lang}" accesskey="r" />
                <input type="submit" name="submitButton" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
            </div>
        </form>
    </div>
    
    <script type="text/javascript">
         /* <![CDATA[ */
            $(document).ready(function() {
                $('#configEditorLink').click(function(event) {
                    event.preventDefault();
                    WCF.showDialog('configEditorPopup', true, {
                       title: '{lang}ultimate.template.topMenu.title{/lang}'
                    });
                });

                $('#configEditorPopupForm').live('submit', function(event) {
                    var action = $(this).find('input[name="action"]').val();
                    if (action == 'add') {
                        if ($('#configID').val() == '0') {
                            event.preventDefault();
                            alert('{lang}ultimate.template.topMenu.configID.empty{/lang}');
                        }
                    }
                });
                $('#configEditorLink input[name=action]').live('change', function(event) {
                    if ($(event.target).val() == 'add') {
                        $('#configID').disable();
                        $('#configID').parents('dl').addClass('disabled');
                    }
                    else {
                        $('#configID').enable();
                        $('#configID').parents('dl').removeClass('disabled');
                    }
                });
                $('#configEditorPopup input[type=reset]').live('click', function(event) {
                    $('#configID').disable();
                    $('#configID').parents('dl').addClass('disabled');
                });
            });
         /* ]]> */
    </script>
</li>
