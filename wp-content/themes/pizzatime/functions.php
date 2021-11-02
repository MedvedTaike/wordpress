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

add_action('init', 'create_global_variable');
add_action( 'after_setup_theme', 'theme_support' );
function theme_support() {
  register_nav_menu( 'menu_main_header', 'Меню в шапке' );
  add_theme_support('post-thumbnails');
}

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

add_action( 'carbon_fields_register_fields', 'crb_attach' );
function crb_attach() {
    Container::make( 'theme_options', __( 'Настройки' ) )
        ->add_tab( 'Общие настройки', [
            Field::make( 'image', 'site_logo', __('Логотип') ),
            Field::make( 'text', 'site_footer_text', 'Текст в подвале' ),
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
        Container::make('post_meta', __('Дополнительные поля'))
         ->show_on_page(8)
         ->add_tab('Первый экран!', [
           Field::make('text', 'top_info', __('Надзаголовок!')),
           Field::make('text', 'top_title', __('Заголовок!')),
           Field::make('text', 'top_btn_text', __('Текст кнопки'))->set_width(50),
           Field::make('text', 'top_btn_scroll_to', __('Класс секции для перехода!'))->set_width(50),
           Field::make('image', 'top_img', __('Изображение')),
         ])
         ->add_tab('Каталог!', [
           Field::make('text', 'catalog_title', __('Заголовок!')),
         ])
         ->add_tab( 'О нас', [
          Field::make( 'text', 'about_title', __('Заголовок') ),
          Field::make( 'rich_text', 'about_text', __('Текст') ),
          Field::make( 'image', 'about_img', __('Изображение') ),
        ])
        ->add_tab( 'Контакты', [
          Field::make( 'text', 'contacts_title', __('Заголовок') ),
          Field::make( 'checkbox', 'contacts_is_img', __('Показать изображение помидоров') )
        ]);

        Container::make( 'post_meta', __('Дополнительные поля') )
        ->show_on_page(21)
        ->add_tab( 'Информация о странице', [
            Field::make( 'media_gallery', 'gallery', __('Галерея') )
        ]);

        Container::make( 'post_meta', __('Дополнительные поля') )
        ->show_on_post_type('product')
        ->add_tab( 'Информация товара', [
          Field::make( 'text', 'product_price', __('Цена') ),
          Field::make( 'complex', 'product_attributes', __('Атрибуты') )
          ->set_max(3)
          ->add_fields([
            Field::make( 'text', 'name', 'Название' )->set_width(50),
            Field::make( 'text', 'price', 'Цена' )->set_width(50),
           ])
      ]);
}

function convertToWebpSrc($src) {
  $src_webp = $src . '.webp';
  return str_replace('uploads', 'uploads-webpc', $src_webp);
}
add_action( 'init', 'register_post_types' );
function register_post_types() {
  register_post_type('product', [
    'labels' => [
      'name'               => 'Товары', // основное название для типа записи
      'singular_name'      => 'Товар', // название для одной записи этого типа
      'add_new'            => 'Добавить товар', // для добавления новой записи
      'add_new_item'       => 'Добавление товара', // заголовка у вновь создаваемой записи в админ-панели.
      'edit_item'          => 'Редактирование товара', // для редактирования типа записи
      'new_item'           => 'Новый товар', // текст новой записи
      'view_item'          => 'Смотреть товар', // для просмотра записи этого типа.
      'search_items'       => 'Искать товар', // для поиска по этим типам записи
      'not_found'          => 'Не найдено', // если в результате поиска ничего не было найдено
      'not_found_in_trash' => 'Не найдено в корзине', // если не было найдено в корзине
      'menu_name'          => 'Товары', // название меню
    ],
    'menu_icon'          => 'dashicons-cart',
    'public'             => true,
    'menu_position'      => 5,
    'supports'           => ['title', 'editor', 'thumbnail', 'excerpt'],
    'has_archive'        => true,
    'rewrite'            => ['slug' => 'products']
   ] );

   register_taxonomy('product-categories', 'product', [
    'labels'        => [
      'name'                        => 'Категории товаров',
      'singular_name'               => 'Категория товаров',
      'search_items'                => 'Искать категории',
      'popular_items'               => 'Популярные категории',
      'all_items'                   => 'Все категории',
      'edit_item'                   => 'Изменить категорию',
      'update_item'                 => 'Обновить категорию',
      'add_new_item'                => 'Добавить новую категорию',
      'new_item_name'               => 'Новое название категории',
      'separate_items_with_commas'  => 'Отделить категории запятыми',
      'add_or_remove_items'         => 'Добавить или удалить категорию',
      'choose_from_most_used'       => 'Выбрать самую популярную категорию',
      'menu_name'                   => 'Категории',
    ],
    'hierarchical'  => true,
  ]);
}