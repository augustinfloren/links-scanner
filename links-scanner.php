<?php
 /** 
 * @package Links Scanner
 * @version 1.0.0
 */
/*
Plugin Name: Links Scanner
Plugin URI: https://github.com/augustinfloren/links-scanner
Description: Un plugin pour scanner les URLs internes de la page d’accueil.
Version: 1.0.0
Author: Augustin Floren
License: GPLv2 or later
Text Domain: links-scanner
*/

function scan_plugin__register_hooks() {
    add_action('admin_menu', 'add_scan_plugin_menu');
    add_action('admin_enqueue_scripts', 'enqueue_scan_plugin_scripts');
    add_action('wp_ajax_scan_button_action', 'handle_scan_button_action');
    add_action('wp_ajax_nopriv_scan_button_action', 'handle_scan_button_action');
}
add_action ('init', 'scan_plugin__register_hooks');

function add_scan_plugin_menu() {
    add_menu_page(
        'Scan des URL internes',  
        'Links Scanner',       
        'manage_options',        
        'scan-plugin',           
        'render_scan_plugin_page',
        'dashicons-search',       
        20                        
    );
}

function render_scan_plugin_page() {
    $nonce = wp_create_nonce('scan_button_action');
    ?>
    <div class="wrap">
        <h1>Links Scanner</h1>
        <p>Cliquer sur le bouton ci-dessous pour commencer le scan de la page.</p>
        <button id="scan-button" data-nonce="<?php echo esc_attr($nonce); ?>">Scanner</button>
    </div>
    <div>
        <ul id="scan-result"></ul>
    </div>
    <?php
}

function enqueue_scan_plugin_scripts($hook_suffix) {
    if ($hook_suffix === 'toplevel_page_scan-plugin') {
        wp_enqueue_style(
            'links-scanner-style',
            plugin_dir_url(__FILE__) . 'assets/style.css', [], '1.0.0'
        );

        wp_enqueue_script(
            'scan-button-script',
            plugin_dir_url(__FILE__) . 'js/scan-button.js',
            array('jquery'),
            null,
            true
        );

        wp_localize_script('scan-button-script', 'scanButton', array(
            'ajax_url' => admin_url('admin-ajax.php'),
        ));
    }
}

function handle_scan_button_action() {
    if (isset($_POST['nonce']) && !wp_verify_nonce($_POST['nonce'], 'scan_button_action')) {
        wp_send_json_error('Invalid Nonce');
        wp_die();
    }
    
    $links = scan_homepage_links();
    $permalinks = get_all_permalinks();
    
    $filteredArray = get_matching_items($permalinks, $links);
    
    if (is_array($links)) {
        wp_send_json_success(wp_send_json_success(  $filteredArray));
    } else {
        wp_send_json_error($links);
    }
}

function get_matching_items(array $simpleArray, array $complexArray): array {
    return array_filter($complexArray, function ($item) use ($simpleArray) {
        return in_array($item['url'], $simpleArray);
    });
}

function scan_homepage_links() {
    $homepage_url = home_url('/');
    $response = wp_remote_get($homepage_url);

    if (is_wp_error($response)) {
        return 'Erreur lors de la récupération de la page : ' . $response->get_error_message();
    }

    $html = wp_remote_retrieve_body($response);

    $dom = new DOMDocument();
    @$dom->loadHTML($html);

    $links = $dom->getElementsByTagName('a');
    $permalinks = [];

    foreach ($links as $link) {
        $href = $link->getAttribute('href');
        $anchor_text = trim($link->textContent);

        if(strpos($href, home_url('/')) === 0) {
            $permalinks[] = [
                'url' => $href,
                'anchor_text' => $anchor_text
            ];
        }
    }

    return $permalinks;
}

function get_all_permalinks() {
    $args = array(
        'post_type' => ['post', 'page'], 
        'posts_per_page' => -1, 
        'post_status' => 'publish', 
    );
    
    $posts = get_posts($args);

    $permalinks = [];

    foreach ($posts as $post) {
        $permalinks[] = get_permalink($post->ID);
    }

    return array_unique($permalinks);
}



