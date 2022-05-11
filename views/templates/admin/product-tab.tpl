{*
* Project : everpstabs
* @author Team EVER
* @copyright Team EVER
* @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
* @link https://www.team-ever.com
*}

<!-- Module Ever PS Tabs -->
<div id="everpstabs" class="panel">
    <fieldset class="form-group">
        <div class="col-lg-12 col-xl-12">
            <div class="translations tabbable">
                <div class="translationsFields tab-content bordered everpstabs">
                    <label for="everpstabs_height">{l s='Tab title' mod='everpstabs'}</label>
                    {foreach from=$ever_languages item=language}
                    <div class="tab-pane translation-label-{$language.iso_code|escape:'htmlall':'UTF-8'} {if $default_language == $language.id_lang}active{/if}">
                           <input type="text" id="everpstabs_title_{$language.id_lang|escape:'htmlall':'UTF-8'}" name="everpstabs_title_{$language.id_lang|escape:'htmlall':'UTF-8'}" {if isset($everpstabs->title[$language.id_lang]) && $everpstabs->title[$language.id_lang]}value="{$everpstabs->title[$language.id_lang]|escape:'htmlall':'UTF-8'}"{/if}>
                       </div>
                    {/foreach}
                    <label class="form-control-label">{l s='Tab content' mod='everpstabs'}</label>
                    {foreach from=$ever_languages item=language}
                        <div class="tab-pane translation-label-{$language.iso_code|escape:'htmlall':'UTF-8'} {if $default_language == $language.id_lang}active{/if}">
                           <textarea id="everpstabs_{$language.id_lang|escape:'htmlall':'UTF-8'}" name="everpstabs_{$language.id_lang|escape:'htmlall':'UTF-8'}" class="form-control autoload_rte">{if isset({$everpstabs->content[$language.id_lang]}) && {$everpstabs->content[$language.id_lang]} != ''}{$everpstabs->content[$language.id_lang]|escape:'htmlall':'UTF-8'}{/if}</textarea>
                       </div>
                    {/foreach}
                </div>
            </div>
        </div>
    </fieldset>
</div>
<!-- /Module Ever PS Tabs -->
