{load_templates "subtemplates/forms.tpl"}

{if $.get.jsdebug || !Site::resolvePath('site-root/js/pages/common.js')}
    <script src="{Sencha::getVersionedFrameworkPath('ext', 'build/ext-all-debug.js', '5.0.0')}"></script>
    {sencha_bootstrap classPaths=array('sencha-workspace/pages/src', 'ext-library/Jarvus/ext/patch')}
{else}
    <script src="{versioned_url 'js/pages/common.js'}"></script>
{/if}

{if !$.User} {* markup for login modal. TODO: generate this in the JS *}
<div class="modal-mask" style="display:none" id="login-modal">
    <form method="post" action="/login" class="modal-dialog">
        {foreach item=value key=name from=$postVars}
            {if is_array($value)}
                {foreach item=subvalue key=subkey from=$value}
                <input type="hidden" name="{$name|escape}[{$subkey|escape}]" value="{$subvalue|escape}">
            {else}
                <input type="hidden" name="{$name|escape}" value="{$value|escape}">
            {/if}
        {/foreach}
        <input type="hidden" name="_LOGIN[returnMethod]" value="{refill field=_LOGIN.returnMethod default=$.server.REQUEST_METHOD}"
        <input type="hidden" name="_LOGIN[return]" value="{refill field=_LOGIN.return default=$.server.REQUEST_URI}">

        <header class="modal-header">
            <div class="modal-close-button">&times;</div>
            <h2 class="modal-title">Log In</h2>
        </header>

        <div class="modal-body">
            <fieldset class="stretch">
                {loginField}
                {passwordField}
            </fieldset>
        </div>

        <footer class="modal-buttons">
            <input type="submit" class="primary" value="Log In">
        </footer>
    </form>
</div>
{/if}

<script>
    Ext.scopeCss = true;
    Ext.require('Site.Common');
</script>