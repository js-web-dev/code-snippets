<?php
define('TWIRE_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('TWIRE_PLUGIN_URL', plugin_dir_url(__FILE__));

/* for minifying and/or combining multiple js/css files into one file include the asset-minifier.php file */
if (file_exists(TWIRE_PLUGIN_PATH . 'lib/asset-minifier.php')) require_once('lib/asset-minifier.php');

/* de- and enqueue files only in/from frontend */
function twire_enqueue_dequeue() {
    /* register/deregister */
    wp_register_script('twire-js', TWIRE_PLUGIN_URL . 'assets/js/twire.js');
    wp_deregister_script('twire-js');
    wp_register_style('twire-css', TWIRE_PLUGIN_URL . 'assets/css/twire.css');
    wp_deregister_style('twire-css');

    /* dequeue */
    wp_dequeue_style('wp-block-library');
    wp_dequeue_script('wp-block-navigation-view');

    /* enqueue */
    wp_enqueue_style('twire-css', TWIRE_PLUGIN_URL . 'assets/css/twire.css', [], filemtime(TWIRE_PLUGIN_PATH . 'assets/css/twire.css'));
    wp_enqueue_script('twire-js', TWIRE_PLUGIN_URL . 'assets/js/twire.js', array(), filemtime(TWIRE_PLUGIN_PATH . 'assets/js/twire.js'), array('in_footer' => true, 'strategy'  => 'defer'));

    /* example of enqueue minified and combined (first include asset-minifier.php in top) */
    wp_enqueue_style('twire-color-schemes-css', TWIRE_PLUGIN_URL . AssetPacker::css(
		'assets/css/color-schemes/js-color-schemes.css',
		[
			'assets/css/color-schemes/light.css',
			'assets/css/color-schemes/blue.css',
			'assets/css/color-schemes/dark.css',
			'assets/css/color-schemes/global.css'
		]
	), [], filemtime(TWIRE_PLUGIN_PATH . 'assets/css/color-schemes/js-color-schemes.css'));

    /* enqueue inline style
        1. enqueue the inline-css file
        2. get file content of the inline-css file and add it as wp_add_inline_style
        3. block the inline-css file in style_loader_tag filter because we just want to inline the style, not inline and extern
    */
    wp_enqueue_style('twire-inline-css', TWIRE_PLUGIN_URL . 'assets/css/twire-inline.css');
    $twireInlineCss = file_get_contents(TWIRE_PLUGIN_URL . 'assets/css/twire-inline.css');
    wp_add_inline_style('twire-inline-css', $twireInlineCss);
    /*
        echoing a var directly into the header is also possible but not that good because it is hard set
        BUT: with echo the inline-css will be at first place in head - maybe this is what is needed
        echo '<style id="twire-inline-css">' . $twireInlineCss . '</style>';
    */

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
}
add_action('wp_enqueue_scripts', 'twire_enqueue_dequeue', 100);
// add_action('enqueue_block_assets', 'twire_enqueue_dequeue');
// add_action('enqueue_block_editor_assets', 'twire_enqueue_dequeue');

/* filter styles */
function twire_style_loader_tag($html, $handle){
    if (!is_admin()) {
        /* block inline-css because we just want to inline the style, not inline and extern */
        if ($handle === 'twire-inline-css') $html = str_replace(' href', ' defer data-inline-href',    $html);
    }
    return $html;
}
add_filter('style_loader_tag',  'twire_style_loader_tag', 10, 2);