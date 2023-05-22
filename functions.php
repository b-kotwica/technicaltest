<?php

/**
 * Enqueue stylesheets
 */
add_action( 'wp_enqueue_scripts', 'fz_theme_enqueue_styles' );
function fz_theme_enqueue_styles() {
	$parenthandle = 'twentytwenty';
	$theme = wp_get_theme();

	wp_enqueue_style( 
		$parenthandle,
		get_template_directory_uri() . '/style.css',
		array(),
		$theme->parent()->get( 'Version' )
	);

	wp_enqueue_style( 
		'fooz-styles',
		get_stylesheet_uri(),
		array( $parenthandle ),
		$theme->get( 'Version' )
	);
}


/**
 * Enqueue scripts
 */
add_action( 'wp_enqueue_scripts', 'fz_theme_enqueue_scripts' );
function fz_theme_enqueue_scripts() {
	wp_enqueue_script(
		'fooz-scripts',
		get_stylesheet_directory_uri() . '/assets/js/scripts.js',
		array('jquery'),
		false,
		true
	);

    wp_localize_script(
    	'fooz-scripts', 
    	'wp_ajax_get_books', 
    	array(
    		'ajax_url' => admin_url('admin-ajax.php'),
    		'nonce'    => wp_create_nonce('fooz-nonce')
    	)
    );
}

/**
 * Include Books CPT
 */

include_once('src/Books.php');	
