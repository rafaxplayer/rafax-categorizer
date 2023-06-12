<?php
defined('ABSPATH') || exit; // Exit if accessed directly.
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}



class Categorizer_List_Table extends WP_List_Table
{
    // Here we will add our code
    private $table_data = [];
   
    public function __construct()
    {
        parent::__construct(
            array(
                'singular' => 'post',
                'plural' => 'posts',
                'ajax' => true
            )
        );

    }
    // Define table columns
    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'id' => __('ID', 'rafax-categorizer'),
            'title' => __('Titulo', 'rafax-categorizer'),
            'cats' => __('Categorias', 'rafax-categorizer'),
            'status' => __('Status', 'rafax-categorizer'),
            'select-cat' => __('Categoria', 'rafax-categorizer'),
            

        );
        return $columns;
    }

    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="post_id[]" value="%s" />',
            $item['id']
        );
    }

    function column_default($item, $column_name)
    {
        $args = array(
            'show_option_all' => __('Categoria individual', 'rafax-categorizer'),
            'show_count' => true,
            'hide_empty' => false,
            'id' => 'cat-'.$item['id'],
            'name'=>'cat-'.$item['id'],
            'value_field' => 'term_id',
            'echo'             => 0,
            'class' =>'select_cat'
        );
        $select_cats = wp_dropdown_categories($args);

        switch ($column_name) {
            case 'id':
            case 'title':
            case 'cats':
            case 'status':
                return $item[$column_name];
            case 'select-cat':
                return $select_cats ;
            default:
                return 'no items';
        }
    }

    protected function get_sortable_columns()
    {
        $sortable_columns = array(

            'title' => array('title', true),
            'cats' => array('cats', true),
            'status' => array('status', true),
        );
        return $sortable_columns;
    }

    function prepare_items()
    {
        $this->process_bulk_action();
        if(isset($_REQUEST['num_items']))
        {
            update_option('categorizer_items_per_page',$_REQUEST['num_items']);
        }

        $search_term = isset($_POST['s']) ? sanitize_text_field(trim($_POST['s'])) : '';
       

        $columns = $this->get_columns();
        $hidden = array('id');
        $sortable = $this->get_sortable_columns();
        $primary='title';
        $this->_column_headers = array($columns, $hidden, $sortable,$primary);

                
        /* pagination */
        $per_page = absint(get_option('categorizer_items_per_page'));
       
        $current_page = $this->get_pagenum();

        $table_data = $this->get_table_data($search_term);

        $total_items = count($table_data);

        usort($table_data, array($this, 'usort_reorder'));

        $this->set_pagination_args(
            array(
                'total_items' => $total_items,
                // total number of items
                'per_page' => $per_page,
                // items to show on a page
                'total_pages' => ceil($total_items / $per_page) // use ceil to round up
            )
        );

        $this->items = array_slice($table_data, (($current_page - 1) * $per_page), $per_page);
       

    }


    public function no_items()
    {
        _e('No hay articulos que coinzidan.', 'rafax-categorizer');
    }


    private function get_table_data($search = '')
    {
        global $wpdb;

        $table = $wpdb->prefix . 'posts';

        $posts = array();
        $final_posts = array();

        $query = "SELECT ID,post_title,post_status FROM {$table} WHERE post_status IN ('publish','draft','pendent') AND post_type ='post'";

        if (!empty($search)) {
            $query .= " AND LOWER(post_title) Like '%{$search}%'";
        }

        $posts = $wpdb->get_results(
            $query,
            ARRAY_A
        );

        if (count($posts) > 0) {

            foreach ($posts as $post) {
                //$title = '<a href="' . get_the_permalink($post['ID']) . '">' . $post['post_title'] . '</a>';
                array_push($final_posts, ['id' => $post['ID'], 'title' => $post['post_title'], 'cats' => get_the_category_list(',', '', $post['ID']), 'status' => $post['post_status']]);
            }
        }

        return $final_posts;

    }

    function usort_reorder($a, $b)
    {
        $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
        $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
        $result = strnatcmp($a[$orderby], $b[$orderby]); //Determine sort order
        return ($order === 'asc') ? $result : -$result; //Send final sort direction to usort
    }

    function column_title($item)
    {
        
        $categorizer_nonce = wp_create_nonce('categorizer_nonce');
        $cat = isset($_POST['cat']) ? absint($_POST['cat']): 0;
        $actions = array(
            'edit' => sprintf('<a href="%s">Edit</a>', esc_url(get_edit_post_link($item['id']))),
            'view' => sprintf('<a href="%s">View</a>', esc_url(get_permalink($item['id']))),
            'categorizer' => sprintf('<a class="rafax-categorizer" href="?page=%s&action=%s&_wpnonce=%s&post_id=%s&cat=%s">Categorizar</a>', esc_attr($_REQUEST['page']), 'categoriz', $categorizer_nonce, absint($item['id']),$cat)
        );

        return sprintf('<strong><a href="%s">%s</a></strong>%s ', esc_url(get_edit_post_link($item['id'])), $item['title'], $this->row_actions($actions));
    }

    protected function extra_tablenav($which)
    {
        if ($which == 'top') {
            $num_value = get_option('categorizer_items_per_page');
            ?>
            <div class="alingleft actions">
            <input name="num_items" id="num_items" type="number" min="1" step="1" max="999" class="screen-per-page" value="<?php echo $num_value;?>">
            
            <?php
            
            $this->search_box('Buscar entradas', 'search_id');
            
            $args = array(
                'show_option_all' => __('Categoria global', 'rafax-categorizer'),
                'show_count' => true,
                'hide_empty' => false,
                'id' => 'cat',
                'selected' => isset($_POST['cat']) ? absint($_POST['cat']): 0,
                'value_field' => 'term_id'
            );
            wp_dropdown_categories($args);
            submit_button(__('Aplicar', 'rafax-categorizer'), '', 'filter_action', false, array('class'=>'button','id' => 'post-query-submit')); 
            ?>
            
            
        <?php 
        }
    }

    function get_bulk_actions()
    {
        $actions = array(
            'categoriz' => __('Categorizar', 'rafax-categorizer')
        );
        return $actions;
    }

    public function process_bulk_action()
    {
        //log_it($_REQUEST, 'Request');
       
        
        
        if ('categoriz' === $this->current_action()) {

            if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'categorizer_nonce')) {
                die('No tienes permisos para hacer esto!!!');

            }

            if (!isset($_REQUEST['post_id']) && !isset($_REQUEST['post_id'])) {
                
                printf('<div class="notice notice-error is-dismissible"><p>%s</p></div>',__('No hay post id','rafax-categorizer'));
                return;
            }

            if (!isset($_REQUEST['cat']) || $_REQUEST['cat'] == 0) {
                
                printf('<div class="notice notice-error is-dismissible"><p>%s</p></div>',__('No has elegido categoria','rafax-categorizer'));
                return;
            }

            $post_ids = isset($_REQUEST['post_id']) ? $_REQUEST['post_id'] : array();
            $cat_id = isset($_REQUEST['cat']) ? $_REQUEST['cat'] : 0;

            if(is_array($post_ids )){
                $retOK = false;
                foreach ($post_ids as $id) {
                    $retOK =  wp_set_post_categories( $id, $cat_id);
                    
                }
                
                $message = $retOK ?__('Posts categorizados','rafax-categorizer'):__('Ocurrio un error al categorizar','rafax-categorizer');
                $type = $retOK ? 'success' :'error';
                printf('<div class="notice notice-%s is-dismissible"><p>%s</p></div>',$type,$message);

            }else{

                $retOK = wp_set_post_categories( $post_ids, $cat_id);
               
                
                $message = $retOK ?__('Post categorizado','rafax-categorizer'):__('Ocurrio un error al categorizar','rafax-categorizer');
                $type = $retOK ? 'success' :'error';
                printf('<div class="notice notice-%s is-dismissible"><p>%s</p></div>',$type,$message);

            }
            

        }
    }

}