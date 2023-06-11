<?php /*
Plugin Name: Rafax Categorizer
Plugin URI: 
Description: Categorizar entradas nunca fue tan facil
Version: 1.0
Author: rafax
Text Domain: rafax-categorizer
Author URI:
License:GPL
*/
defined('ABSPATH') || exit; // Exit if accessed directly.
class RFX_Categorizer
{
    private static $instance;

    public $version = '1.0';
    private function __construct()
    {
    }

    public static function instance()
    {
        if (!isset(self::$instance) && !(self::$instance instanceof RFX_Catgorizer)) {
            self::$instance = new RFX_Categorizer;

            self::$instance->setup_constants();

            self::$instance->includes();

            self::$instance->actions();


        }
        return self::$instance;
    }
    private function setup_constants()
    {
        define('RAFAX_CATEGORZER_NAME', 'rafax-categorizer');

        // Plugin Folder PATH
        define('RAFAX_CATEGORZER_DIR', plugin_dir_path(__FILE__));

        // Plugin Folder URL
        define('RAFAX_CATEGORZER_URL', plugin_dir_url(__FILE__));

        // Plugin Root File
        define('RAFAX_CATEGORZER_FILE', __FILE__);
    }

    private function includes()
    {
        require_once 'inc/enqueue-scripts.php';
        require_once 'inc/database.php';
        require_once 'inc/list-table.php';
    }

    private function actions()
    {

        add_action('admin_menu', array($this, 'admin_page'));
        add_option('categorizer_items_per_page', '10', false);

    }

    public function admin_page()
    {
        add_menu_page('Rafax Categorizer', 'Rafax Categorizer', 'publish_posts', RAFAX_CATEGORZER_NAME, array($this, 'rafax_categoriz_admin_page'), '');

    }

    public function rafax_categoriz_admin_page()
    {
        if (isset($_POST['cat_name']) && wp_verify_nonce($_POST['_wpnonce'], 'create_category')) {
            $cat_name = sanitize_text_field($_POST['cat_name']);
            $cat = wp_create_category($cat_name);
            $message = $cat > 0 ?'Categoria <b>%s</b> creada con exito!' : 'Ocurrio un error al crear la categoria %s';
            $type = $cat > 0 ? 'success' : 'error';
            printf('<div class="notice notice-%s is-dismissible"><p>%s</p></div>', $type, sprintf(__($message, 'rafax-categorizer'), $cat_name));

        }
        ?>
        <div class="wrap">
            <h2> Categorizer by rafax</h2>
            <table class="form-table">
                <tr>
                    <td style="width:30%">
                        <form method="post">
                            <label for="cat_create">Crear categoria</label></br>
                            <input name="cat_name" id="cat_name" type="text">
                            <?php wp_nonce_field('create_category');
                            submit_button(__('Crear', 'rafax-categorizer'), '', 'cat_create', false, array('id' => 'cat_create')); ?>

                        </form>
                    </td>
                </tr>
            </table>
            <form id="categorizer_list" class="form-table" method="post">
                <?php
                $class = new Categorizer_List_Table;
                $class->prepare_items();
                $class->display();
                wp_nonce_field('categorizer_nonce');
                ?>
            </form>
        </div>
    <?php }
}
function RFX_CATEGORIZER()
{
    return RFX_Categorizer::instance();
}
RFX_CATEGORIZER();