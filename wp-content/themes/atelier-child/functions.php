<?php
echo "tuffon:";
echo get_template_directory_uri();
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/rtl.min.css' );
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/css/main.css' );
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/css/responsive.css' );
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/css/sf-combined.min.css' );
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/css/sf-woocommerce.css' );
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/css/iconmind.css' );
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/css/font-awesome.min.css' );
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/css/editor-style.css' );
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/css/bootstrap.min.css' );
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/css/bbpress.css' );

}