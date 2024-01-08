<?php

/** de- and enqueue files into blockeditor AND frontend */
function twire_enqueue_dequeue() {
    $twireColorSchemeInlineJs = <<<EOT
                                (noDelay = (_public, undefined) => {
                                    _public.getLocalStorageValue = (key) => {
                                        if (localStorage.getItem(key)) return localStorage.getItem(key)
                                        else if (key === _public.colorSchemeLocalStorageKey) return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
                                    }
                                    _public.setLocalStorageValue = (key, newValue) => {
                                        localStorage.setItem(key, newValue)
                                        _public.currentColorScheme.value = newValue
                                        _public.setHtmlAttribute(_public.colorSchemeLocalStorageKey, newValue)
                                    }
                                    _public.setHtmlAttribute = (key, value) => {document.firstElementChild.setAttribute(key, value)}
                                    _public.colorSchemeLocalStorageKey = 'LOCAL STORAGE KEY' /* e.g. 'twire-color-scheme' */
                                    _public.currentColorScheme = {value: _public.getLocalStorageValue(_public.colorSchemeLocalStorageKey)}
                                    _public.setHtmlAttribute(_public.colorSchemeLocalStorageKey, _public.currentColorScheme.value)
                                })(window.twirePublic = window.twirePublic || {})
                                EOT;
    wp_register_script('twire-color-scheme-js', false);
    wp_enqueue_script('twire-color-scheme-js');
    wp_add_inline_script('twire-color-scheme-js', $twireColorSchemeInlineJs);
}
add_action('enqueue_block_assets', 'twire_enqueue_dequeue', 1);
// add_action('enqueue_block_editor_assets', 'js_enqueue_dequeue');