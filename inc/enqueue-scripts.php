<?php 
defined('ABSPATH') || exit; // Exit if accessed directly.
function rafax_categorizer_scripts(){

    wp_enqueue_style(RAFAX_CATEGORZER_NAME . '-', RAFAX_CATEGORZER_URL . '/assets/css/categoriz.css');
    wp_enqueue_script(RAFAX_CATEGORZER_NAME . '-', RAFAX_CATEGORZER_URL . '/assets/js/categoriz.js', array('jquery'));
    /* wp_localize_script(
		RAFAX_CATEGORZER_NAME . '-',
		'AjaxParams',
		array(
			'adminAjaxUrl' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('categorizer_nonce'),
		)
	); */
}

//if (strpos($_SERVER['REQUEST_URI'], "rafax-categorizer") !== false) {
    
    add_action('admin_enqueue_scripts', 'rafax_categorizer_scripts');
//}