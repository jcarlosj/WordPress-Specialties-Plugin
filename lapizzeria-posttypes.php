<?php
/**
 * Plugin Name:       La Pizzeria: Especialidades
 * Plugin URI:        
 * Description:       Agrega Especialidades al sitio web La Pizzería (Custom Post Types)
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Juan Carlos Jiménez Gutiérrez
 * Author URI:        https://github.com/jcarlosj
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
*/

if( ! defined( 'ABSPATH' ) )  exit;     //  Evita acceso al codigo del plugin

/** Define Custom Post Type para Especialidades de La Pizzeria */
function lapizzeria_add_cpt_specialties() {
	$labels = array(
        'name' => _x( 'Specialties', 'lapizzeria' ),
        'singular_name'         => _x( 'Specialty', 'post type singular name', 'lapizzeria' ),
        'menu_name'             => _x( 'Specialties', 'admin menu', 'lapizzeria' ),
        'name_admin_bar'        => _x( 'Specialties', 'add new on admin bar', 'lapizzeria' ),
        'add_new'               => _x( 'Add New', 'book', 'lapizzeria' ),
        'add_new_item'          => __( 'Add Specialty', 'lapizzeria' ),
        'new_item'              => __( 'New Specialty', 'lapizzeria' ),
        'edit_item'             => __( 'Edit Specialty', 'lapizzeria' ),
        'view_item'             => __( 'View Specialty', 'lapizzeria' ),
        'all_items'             => __( 'All Specialties', 'lapizzeria' ),
        'search_items'          => __( 'Search Specialties', 'lapizzeria' ),
        'parent_item_colon'     => __( 'Parent Specialty', 'lapizzeria' ),
        'not_found'             => __( 'No specialties found', 'lapizzeria' ),
        'not_found_in_trash'    => __( 'No specialties found', 'lapizzeria' )  
	);

	$args = array(  
		'labels'             => $labels,
        'description'        => __( 'Description.', 'lapizzeria' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'lapizzeria-specialty-menu' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 6,
		'supports'           => array( 'title', 'editor', 'thumbnail' ),
		'taxonomies'         => array( '' )
	);

    #   Registra el Custom Post Type
	register_post_type( 'specialties', $args );
}
add_action( 'init', 'lapizzeria_add_cpt_specialties' );

