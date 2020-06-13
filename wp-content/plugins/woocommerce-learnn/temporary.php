<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 10/04/2016
 * Time: 10:27
 */
array(
    'labels'              => array(
        'name'                  => __( 'Questions', 'woocommerce' ),
        'singular_name'         => __( 'Question', 'woocommerce' ),
        'menu_name'             => _x( 'Questions', 'Admin menu name', 'woocommerce' ),
        'add_new'               => __( 'Add Question', 'woocommerce' ),
        'add_new_item'          => __( 'Add New Question', 'woocommerce' ),
        'edit'                  => __( 'Edit', 'woocommerce' ),
        'edit_item'             => __( 'Edit Question', 'woocommerce' ),
        'new_item'              => __( 'New Question', 'woocommerce' ),
        'view'                  => __( 'View Question', 'woocommerce' ),
        'view_item'             => __( 'View Question', 'woocommerce' ),
        'search_items'          => __( 'Search Questions', 'woocommerce' ),
        'not_found'             => __( 'No Questions found', 'woocommerce' ),
        'not_found_in_trash'    => __( 'No Questions found in trash', 'woocommerce' ),
        'parent'                => __( 'Parent Question', 'woocommerce' ),
        'featured_image'        => __( 'Question Image', 'woocommerce' ),
        'set_featured_image'    => __( 'Set product image', 'woocommerce' ),
        'remove_featured_image' => __( 'Remove product image', 'woocommerce' ),
        'use_featured_image'    => __( 'Use as product image', 'woocommerce' ),
    ),
    'description'         => __( 'This is where you can add new products to your store.', 'woocommerce' ),
    'public'              => true,
    'show_ui'             => true,
    'capability_type'     => 'product',
    'map_meta_cap'        => true,
    'publicly_queryable'  => true,
    'exclude_from_search' => false,
    'hierarchical'        => false, // Hierarchical causes memory issues - WP loads all records!
    'query_var'           => true,
    'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'custom-fields', 'page-attributes', 'publicize', 'wpcom-markdown' ),
    'show_in_nav_menus'   => true
)