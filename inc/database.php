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

    if (!wp_verify_nonce('categorizer_nonce') && !empty($cat)) {
        return;
    }
    if (!isset($_POST['cat_name'])) {
        return;
    }

    return wp_create_category($_POST['cat_name']);
    wp_die();
}