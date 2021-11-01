<?php

add_filter('show_admin_bar', '__return_false');

remove_action('wp_head',             'print_emoji_detection_script', 7 );
remove_action('admin_print_scripts', 'print_emoji_detection_script' );
remove_action('wp_print_styles',     'print_emoji_styles' );
remove_action('admin_print_styles',  'print_emoji_styles' );

remove_action('wp_head', 'wp_resource_hints', 2 ); //remove dns-prefetch
remove_action('wp_head', 'wp_generator'); //remove meta name="generator"
remove_action('wp_head', 'wlwmanifest_link'); //remove wlwmanifest
remove_action('wp_head', 'rsd_link'); // remove EditURI
remove_action('wp_head', 'rest_output_link_wp_head');// remove 'https://api.w.org/
remove_action('wp_head', 'rel_canonical'); //remove canonical
remove_action('wp_head', 'wp_shortlink_wp_head', 10); //remove shortlink
remove_action('wp_head', 'wp_oembed_add_discovery_links'); //remove alternate

add_action('wp_enqueue_scripts', 'site_scripts');
function site_scripts() {
  $version = '0.0.0.0';
  wp_dequeue_style( 'wp-block-library' );
  wp_deregister_script( 'wp-embed' );

  wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css?family=Montserrat:900%7CRoboto:300&display=swap&subset=cyrillic', [], $version);
  wp_enqueue_style('main-style', get_stylesheet_uri(), [], $version);

  wp_enqueue_script('focus-visible', 'https://unpkg.com/focus-visible@5.0.2/dist/focus-visible.js', [], $version, true);
  wp_enqueue_script('lazy-load', 'https://cdn.jsdelivr.net/npm/vanilla-lazyload@12.4.0/dist/lazyload.min.js', [], $version, true);
  wp_enqueue_script('main-js', get_template_directory_uri() . '/assets/js/main.js', ['focus-visible', 'lazy-load'], $version, true);

  wp_localize_script('main-js', 'WPJS', [
    'siteUrl' => get_template_directory_uri(),
  ]);
}

add_action( 'after_setup_theme', 'theme_support' );
function theme_support() {
  register_nav_menu( 'menu_main_header', 'Меню в шапке' );
}

add_action('init', 'create_global_variable');
function create_global_variable() {
  global $pizza_time;
  $pizza_time = [
    'phone' => carbon_get_theme_option( 'site_phone' ),
    'phone_digits' => carbon_get_theme_option( 'site_phone_digits' ),
    'address' => carbon_get_theme_option( 'site_address' ),
    'map_coordinates' => carbon_get_theme_option( 'site_map_coordinates' ),
    'vk_url' => carbon_get_theme_option( 'site_vk_url' ),
    'fb_url' => carbon_get_theme_option( 'site_fb_url' ),
    'inst_url' => carbon_get_theme_option( 'site_inst_url' ),
  ];
}
use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action( 'carbon_fields_register_fields', 'crb_attach_theme_options' );
function crb_attach_theme_options() {
    Container::make( 'theme_options', __( 'Theme Options' ) )
        ->add_tab( 'Общие настройки', [
            Field::make( 'image', 'site_logo', __('Логотип') ),
         ])
        ->add_tab( 'Контакты', [
            Field::make( 'text', 'site_phone', __( 'Телефон' )  ),
            Field::make( 'text', 'site_phone_digit', __('Цифры телефона') ),
            Field::make( 'text', 'site_address', __('Адрес') ),
            Field::make( 'text', 'site_map_coordinates', __('Координаты сайта') ),
            Field::make( 'text', 'site_vk_url', __('В контакте') ),
            Field::make( 'text', 'site_fb_url', __('Фейсбук') ),
            Field::make( 'text', 'site_inst_url', __('Инстаграм') ),
         ]);
}