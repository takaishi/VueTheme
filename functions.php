<?php

function rt_rest_theme_scripts()
{

    wp_enqueue_style('style', get_template_directory_uri() . '/dist/style.css');

    $base_url = esc_url_raw(home_url());
    $base_path = rtrim(parse_url($base_url, PHP_URL_PATH), '/');

    wp_enqueue_script('rest-theme-vue/manifest.js', get_template_directory_uri() . '/dist/scripts/manifest.js', null, null, true);
    wp_enqueue_script('rest-theme-vue/vendor.js', get_template_directory_uri() . '/dist/scripts/vendor.js', null, null, true);
    wp_enqueue_script('rest-theme-vue/main.js', get_template_directory_uri() . '/dist/scripts/main.js', null, null, true);

    wp_localize_script('rest-theme-vue/main.js', 'rtwp', array(
        'root' => esc_url_raw(rest_url()),
        'base_url' => $base_url,
        'base_path' => $base_path ? $base_path . '/' : '/',
        'nonce' => wp_create_nonce('wp_rest'),
        'site_name' => get_bloginfo('name'),
        'site_description' => get_bloginfo('description'),
    ));
}

add_action('wp_enqueue_scripts', 'rt_rest_theme_scripts');

if (function_exists('register_nav_menus')) {

    register_nav_menus(
        array(
            'primary-menu' => __('Primary Menu'),
            'secondary-menu' => __('Secondary Menu'),
        )
    );

}

add_filter('excerpt_more', '__return_false');

add_action('after_setup_theme', 'rt_theme_setup');

function rt_theme_setup()
{

    add_theme_support('post-thumbnails');

}

// Polyfill for wp_title()
add_filter('wp_title', 'rt_vue_title', 10, 3);

function rt_vue_title($title, $sep, $seplocation)
{

    if (false !== strpos($title, __('Page not found'))) {

        $replacement = ucwords(str_replace('/', ' ', $_SERVER['REQUEST_URI']));
        $title = str_replace(__('Page not found'), $replacement, $title);

    }

    return $title;
}

// Extend rest response
add_action('rest_api_init', 'rt_extend_rest_post_response');

function rt_extend_rest_post_response()
{

    // Add featured image
    register_rest_field('post',
        'featured_image_src', //NAME OF THE NEW FIELD TO BE ADDED - you can call this anything
        array(
            'get_callback' => 'get_image_src',
            'update_callback' => null,
            'schema' => null,
        )
    );

    register_rest_field('post',
        'cat_name', //NAME OF THE NEW FIELD TO BE ADDED - you can call this anything
        array(
            'get_callback' => 'rt_get_cat_name',
            'update_callback' => null,
            'schema' => null,
        )
    );

    register_rest_field('post',
        'tag_name',
        array(
            'get_callback' => 'rt_get_tag_name',
            'update_callback' => null,
            'schema' => null,
        )
    );

}

// Get featured image
function get_image_src($object, $field_name, $request)
{

    $feat_img_array['full'] = wp_get_attachment_image_src($object['featured_media'], 'full', false);
    $feat_img_array['thumbnail'] = wp_get_attachment_image_src($object['featured_media'], 'thumbnail', false);
    $feat_img_array['srcset'] = wp_get_attachment_image_srcset($object['featured_media']);
    $image = is_array($feat_img_array) ? $feat_img_array : 'false';
    return $image;

}

function rt_get_cat_name($object, $field_name, $request)
{

    $cats = $object['categories'];
    $res = [];
    $ob = [];
    foreach ($cats as $x) {
        $cat_id = (int)$x;
        $cat = get_category($cat_id);
        if (is_wp_error($cat)) {
            $res[] = '';
        } else {
            $ob['name'] = isset($cat->name) ? $cat->name : '';
            $ob['id'] = isset($cat->term_id) ? $cat->term_id : '';
            $ob['slug'] = isset($cat->slug) ? $cat->slug : '';
            $res[] = $ob;
        }
    }
    return $res;

}

function rt_get_tag_name($object, $field_name, $request)
{

    $tags = $object['tags'];
    $res = [];
    $ob = [];

    foreach ($tags as $x) {
        $tag_id = (int)$x;
        $tag = get_tag($tag_id);
        if (is_wp_error($tag)) {
            $res[] = '';
        } else {
            $ob['name'] = isset($tag->name) ? $tag->name : '';
            $ob['id'] = isset($tag->term_id) ? $tag->term_id : '';
            $ob['slug'] = isset($tag->slug) ? $tag->slug : '';
            $res[] = $ob;
        }
    }
    return $res;
}
