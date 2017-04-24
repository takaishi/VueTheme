<?php

function rest_theme_scripts() {

	wp_enqueue_style( 'style', get_stylesheet_uri() );

	$base_url  = esc_url_raw( home_url() );
	$base_path = rtrim( parse_url( $base_url, PHP_URL_PATH ), '/' );
	wp_enqueue_style( 'rt-theme-font', get_template_directory_uri() . '/fontello/css/fontello.css' );
	if ( defined( 'RT_VUE_DEV' ) && RT_VUE_DEV ) {
			wp_enqueue_script( 'rest-theme-vue', 'http://localhost:8080/dist/build.js', array( 'jquery', 'wp-api' ), '1.0.0', true );
	} else {
		wp_enqueue_script( 'rest-theme-vue', get_template_directory_uri() . '/dist/build.js', array( 'jquery', 'wp-api' ), '1.0.0', true );
	}
	wp_localize_script( 'rest-theme-vue', 'rtwp', array(
		'root'      => esc_url_raw( rest_url() ),
		'base_url'  => $base_url,
		'base_path' => $base_path ? $base_path . '/' : '/',
		'site_name' => get_bloginfo( 'name' ),
	) );

	wp_localize_script( 'wp-api', 'wpApiSettings', array(
		'root' => esc_url_raw( rest_url() ),
		'nonce' => wp_create_nonce( 'wp_rest' ),
	) );
}

add_action( 'wp_enqueue_scripts', 'rest_theme_scripts' );

if ( function_exists( 'register_nav_menus' ) ) {
	register_nav_menus(
		array(
		'primary-menu' => __( 'Primary Menu' ),
		'secondary-menu' => __( 'Secondary Menu' ),
		)
	);
}

add_filter( 'excerpt_more', '__return_false' );

add_action( 'after_setup_theme', 'rt_theme_setup' );

function rt_theme_setup() {
	global $wp_rewrite;
	$wp_rewrite->permalink_structure = $wp_rewrite->root . 'blog/%postname%/';
	$wp_rewrite->page_structure      = $wp_rewrite->root . 'page/%pagename%/';
	$wp_rewrite->front               = $wp_rewrite->root . 'blog/';
	$wp_rewrite->add_rule( '^blog', 'index.php', 'top' );
}

/**
 * Polyfill for wp_title()
 */
add_filter( 'wp_title','rt_vue_title', 10, 3 );

function rt_vue_title( $title, $sep, $seplocation ) {

	if ( false !== strpos( $title, __( 'Page not found' ) ) ) {
		$replacement = ucwords( str_replace( '/', ' ', $_SERVER['REQUEST_URI'] ) );
		$title       = str_replace( __( 'Page not found' ), $replacement, $title );
	}
	return $title;
}

add_action( 'rest_api_init', 'rt_get_next_post' );

function rt_get_next_post() {

	register_rest_route( 'rtvue/v1', '/next/post/(?P<id>[\d]+)', array(
		'methods' => 'GET',
		'callback' => 'rt_next_post',
	));
}
function rt_next_post( WP_REST_Request $request ) {
	$id = $request->get_param( 'id' );
	$args = array(
		'p' => $id,
	);
	$query = new WP_Query( $args );
	$res = [];
	if ( $query->have_posts() ) {
		$query->the_post();
		$res['next'] = get_next_post();
	}
	wp_reset_postdata();
	return $res;
}