<?php
defined('ABSPATH') || exit;


/* add_action('wp_ajax_get_posts_title', 'rafax_categorizer_get_posts');

function rafax_categorizer_get_posts()
{
    if (!isset($_POST['post_key'])) {
        return;
    }

    $key = $_POST['post_key'];

    $wp_query = new WP_Query();

    $posts = $wp_query->query(
        array(
            'post_type' => 'post',
            's' => $key,
            'post_status' => array('draft', 'publish', 'pending', 'future', 'private'),
        )
    );

    wp_send_json($posts);
    wp_die();
} */

add_action('wp_ajax_create_category', 'rafax_categorizer_create_cat');
function rafax_categorizer_create_cat()
{
    
    if (!isset($_POST['nonce'])  || !wp_verify_nonce($_POST['nonce'],'categorizer_nonce') || !current_user_can( 'edit_posts' )) {
        die('No tienes permisos para crear categorias');
        
    }
    if (!isset($_POST['cat_name'])) {
        die('No llego nombre de categoria');
        
    }

    $name_cat = sanitize_text_field($_POST['cat_name']);

   echo wp_create_category($name_cat);
   
    wp_die();
}