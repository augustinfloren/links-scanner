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
    <div id="links-scanner">
        <h1><span class="dashicons dashicons-search"></span> Links Scanner</h1>
        <p>Cliquer sur le bouton ci-dessous pour commencer le scan de la page.</p>
        <div id="scan-button-container">
            <button id="scan-button" data-nonce="<?php echo esc_attr($nonce); ?>">Scanner</button>
            <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
        </div>
        <h4 class="result-count"></h4>
        <div id="scan-result-container">
            <table id="scan-result">
                <thead>
                    <tr>
                        <th style="width: 20%;">Texte</th>
                        <th style="width: 30%;">Lien</th>
                        <th style="width: 20%;">Titre Post/Page</th>
                        <th style="width: 15%;">ID Post/Page</th>
                        <th style="width: 15%;">Statut</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
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
    
    if (is_array($links)) {
        wp_send_json_success(wp_send_json_success(  $links));
    } else {
        wp_send_json_error($links);
    }
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
        if (check_if_permalink_exists($href)) {
            $anchor_text = trim($link->textContent);
            $post_id = url_to_postid($href);
            $title = get_the_title($post_id);
            $status = test_url_status($href);
                if(strpos($href, home_url('/')) === 0) {
                    $permalinks[] = [
                        'url' => $href,
                        'anchor_text' => $anchor_text,
                        'title' => $title,
                        'post_id' => $post_id > 0 ? $post_id : null,
                        'status' => $status
                    ];
                }
            }
    }
    
    return $permalinks;
}

function test_url_status($url) {
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_NOBODY, true);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  

    curl_exec($ch);

    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    return $status_code;  
}


function check_if_permalink_exists($url) {
    $post_id = url_to_postid($url);
    return $post_id > 0;
}



