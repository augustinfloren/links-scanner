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
        'Scanner les URLs',       
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
        <p>Cliquer sur Scan pour commencer le traitement des URLs.</p>
        <button id="scan-button" data-nonce="<?php echo esc_attr($nonce); ?>">Scan</button>
    </div>
    <?php
}

function enqueue_scan_plugin_scripts($hook_suffix) {
    if ($hook_suffix === 'toplevel_page_scan-plugin') {
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
    $response_message = "Success";
    wp_send_json_success($response_message);
}

