<?php
/**
 * Autorun
 */
Books::init();

class Books {


    const POST_TYPE = 'books';
    const TAXONOMY_TYPE = 'books_genre';
    const TEXT_DOMAIN = 'twentytwentyfooz';


    /**
     * Initializes Books features
     * Wordpress hooks
     */
    public static function init() {

        // Registers CPT and Taxonomy
        add_action( 'init', ['Books', 'registerEntities'] );

        // Sets 5 pages per page in Genre Taxonomy
        add_action( 'pre_get_posts', ['Books', 'booksPerPage'] );

        // Shortcodes
        add_shortcode('recent_book', ['Books', 'getMostRecentBook']);
        add_shortcode('book_genre', ['Books', 'getBooksByGenre']);

        // Ajax Callbacks
        add_action('wp_ajax_get_books', ['Books', 'getBooksAjax']);
        add_action('wp_ajax_nopriv_get_books', ['Books', 'getBooksAjax']);
    }


    /**
     * Registers entities for Books
     * including custom post type, taxonomies, etc.
     */
    public static function registerEntities() {

        register_post_type( self::POST_TYPE, [
            'labels' => [
                'name'                  => _x( 'Books', self::TEXT_DOMAIN ),
                'singular_name'         => _x( 'Book', self::TEXT_DOMAIN ),
                'menu_name'             => _x( 'Books', self::TEXT_DOMAIN ),
                'name_admin_bar'        => _x( 'Book', self::TEXT_DOMAIN ),
                'add_new'               => __( 'Add New', self::TEXT_DOMAIN ),
                'add_new_item'          => __( 'Add New Book', self::TEXT_DOMAIN ),
                'new_item'              => __( 'New Book', self::TEXT_DOMAIN ),
                'edit_item'             => __( 'Edit Book', self::TEXT_DOMAIN ),
                'view_item'             => __( 'View Book', self::TEXT_DOMAIN ),
                'all_items'             => __( 'All Books', self::TEXT_DOMAIN ),
                'search_items'          => __( 'Search Books', self::TEXT_DOMAIN ),
                'parent_item_colon'     => __( 'Parent Books:', self::TEXT_DOMAIN ),
                'not_found'             => __( 'No books found.', self::TEXT_DOMAIN ),
                'not_found_in_trash'    => __( 'No books found in Trash.', self::TEXT_DOMAIN ),
                'featured_image'        => _x( 'Book Cover Image', self::TEXT_DOMAIN ),
                'set_featured_image'    => _x( 'Set cover image', self::TEXT_DOMAIN ),
                'remove_featured_image' => _x( 'Remove cover image', self::TEXT_DOMAIN ),
                'use_featured_image'    => _x( 'Use as cover image', self::TEXT_DOMAIN ),
                'archives'              => _x( 'Book archives',  self::TEXT_DOMAIN ),
                'uploaded_to_this_item' => _x( 'Uploaded to this book', self::TEXT_DOMAIN ),
                'filter_items_list'     => _x( 'Filter books list', self::TEXT_DOMAIN ),
                'items_list_navigation' => _x( 'Books list navigation', self::TEXT_DOMAIN ),
                'items_list'            => _x( 'Books list', self::TEXT_DOMAIN ),
            ],
            'public' => true,
            'show_ui' => true,
            'capability_type' => 'page',
            'hierarchical' => false,
            'rewrite'  => array('slug' => 'library', 'with_front' => true),
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'),
            'menu_icon' => 'dashicons-book-alt',

        ]);

        register_taxonomy( self::TAXONOMY_TYPE, self::POST_TYPE, [
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'show_in_rest' => true,
            'rewrite' => ['slug' => 'book-genre', 'hierarchical' => 'true'],
            'labels' => [
                'name'              => _x( 'Genres', self::TEXT_DOMAIN ),
                'singular_name'     => _x( 'Genre', self::TEXT_DOMAIN ),
                'search_items'      => __( 'Search Genres', self::TEXT_DOMAIN ),
                'all_items'         => __( 'All Genres', self::TEXT_DOMAIN ),
                'view_item'         => __( 'View Genre', self::TEXT_DOMAIN ),
                'parent_item'       => __( 'Parent Genre', self::TEXT_DOMAIN ),
                'parent_item_colon' => __( 'Parent Genre:', self::TEXT_DOMAIN ),
                'edit_item'         => __( 'Edit Genre', self::TEXT_DOMAIN ),
                'update_item'       => __( 'Update Genre', self::TEXT_DOMAIN ),
                'add_new_item'      => __( 'Add New Genre', self::TEXT_DOMAIN ),
                'new_item_name'     => __( 'New Genre Name', self::TEXT_DOMAIN ),
                'not_found'         => __( 'No Genres Found', self::TEXT_DOMAIN ),
                'back_to_items'     => __( 'Back to Genres', self::TEXT_DOMAIN ),
                'menu_name'         => __( 'Genre', self::TEXT_DOMAIN ),
            ]
        ]);
    }


    /**
     * Sets 5 books per page in Genre Taxonomy
     */
    public static function booksPerPage($query) {

        if (is_tax( self::TAXONOMY_TYPE ) ) { 
            $query->set( 'posts_per_page', 5 );
        }

    }

    /**
     * Get the most recent book title
     */
    public static function getMostRecentBook() {

        $args = array(
            'post_type'      => Books::POST_TYPE,
            'posts_per_page' => 1,
            'orderby'        => 'date',
            'order'          => 'DESC',
        );

        $query = new WP_Query($args);

        if ($query->have_posts()) {

            while ($query->have_posts()) {
                $query->the_post();
                
                the_title(); 

            }

            wp_reset_postdata();

        } else {

            echo 'No recent books.';

        }

    }

    /**
     * Gets 5 books from indicated genre
     */
    public static function getBooksByGenre($atts) {

        $atts = shortcode_atts(array(
            'term' => '', // term ID
        ), $atts, 'book_genre');

        $args = array(
            'post_type'      => Books::POST_TYPE,
            'posts_per_page' => 5,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'tax_query'      => array(
                array(
                    'taxonomy' => Books::TAXONOMY_TYPE,
                    'field'    => 'term_id',
                    'terms'    => $atts['term'],
                ),
            ),
        );

        $query = new WP_Query($args);

        if ($query->have_posts()) {
            $output = '<ul>';

            while ($query->have_posts()) {

                $query->the_post();
                $output .= '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';

            }

            $output .= '</ul>';

            wp_reset_postdata();

            return $output;

        } else {

            return 'No books found.';

        }
    }

    /**
     * Gets the book terms list
     */
    public static function getBookTerms($post_id) {

        $book_terms = get_the_terms( $post_id , Books::TAXONOMY_TYPE );
        $slugs = array();
        
        foreach ($book_terms as $term) {
            if ( isset($term->slug) ) {
                $slugs[] = $term->slug;
            }
        }

        if (!empty($slugs)) {

            $terms = implode(', ', $slugs);
            return $terms;
        }
    }


    /**
     * Gets the 20 books list as json
     */
    public static function getBooksAjax() {

        if (!check_ajax_referer('fooz-nonce', 'nonce')) {
            wp_die();
        }

        $args = array(
            'post_type'      => Books::POST_TYPE,
            'posts_per_page' => 20,
        );

        $query = new WP_Query($args);
        $books = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {

                $query->the_post();

                $book = array(
                    'name'    => get_the_title(),
                    'date'    => get_the_date(),
                    'genre'   => self::getBookTerms( get_the_ID() ),
                    'excerpt' => get_the_excerpt(),
                );
                $books[] = $book;

            }
        }

        wp_reset_postdata();

        wp_send_json($books);
    }
}
