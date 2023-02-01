<?php
	add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
	function my_theme_enqueue_styles() {
		wp_enqueue_style( 'listingpr-parent-style', get_template_directory_uri() . '/style.css' );
		wp_enqueue_style( 'style-videopopup', get_stylesheet_directory_uri() . '/assets/css/custom.css' );
    	wp_enqueue_style( 'style-splide', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@2.4.20/dist/css/themes/splide-sea-green.min.css' );
    	wp_enqueue_script( 'script-splide', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@2.4.20/dist/js/splide.min.js',array(),1.0, true );
		wp_enqueue_script( 'script-splide-custom', get_stylesheet_directory_uri() . '/assets/js/custom.js',array(),1.0, true );
	}
	

/* ============== Include Visual Composer Widgets ============ */
require_once('include/vc_shortcodes.php');

// Disable the Block Editor (Gutenberg)        
add_filter('use_block_editor_for_post', '__return_false');