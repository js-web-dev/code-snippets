<?php

/** filter scripts and add or edit attributes */
function twire_script_loader_tag($tag, $handle, $src)
{
    if (is_admin()) return $tag;
    if (is_user_logged_in()) return $tag;
    if (false === strpos($tag, '.js')) return $tag;

    if ($handle === 'twire-js' && wp_is_mobile()) $tag = str_replace(' src', ' defer data-delay-src', $tag);
    if (false === stripos($tag, 'defer')) $tag = str_replace('<script ', '<script defer ', $tag);

    return $tag;
}
add_filter('script_loader_tag', 'twire_script_loader_tag', 10, 3);

/** filter styles and add or edit attributes */
function twire_style_loader_tag($html, $handle)
{
    if (is_admin()) return $html;
    if (is_user_logged_in()) return $html;
    if (false === strpos($html, '.css')) return $html;

    if ($handle === 'twire-inline-css') $html = str_replace(' href', ' defer data-inline-href', $html);
    if ($handle === 'twire-figcaption-css') $html = str_replace(' href', ' defer data-delay-href', $html);
    return $html;
}
add_filter('style_loader_tag',  'twire_style_loader_tag', 10, 2);

/*
    if (is_admin()) {};
    if (is_login()) {};
    if (is_user_logged_in()) {};
    if (is_page_template('wp-custom-template-templatename')) {};
    if (wp_is_mobile()) {};
    if (is_page('Contact')) {};
    if (is_page(22)) {};
    if (is_page([1, 4])) {};
    if (is_home()) {};
    if (is_front_page()) {}; // Settings -> Reading -> Your homepage
    if (is_singular()) {}; // $post_types = ... -> checks if the query is for an existing single post of any post type
    if (is_single()) {}; // $post = ... –> determines whether the query is for an existing single post. Similar to is_page()
    if (is_author()) {};
    if (is_attachment()) {};
    if (is_search()) {};
    if (is_404()) {};
*/