<?php
/**
 * Plugin Name:       La Pizzeria: Especialidades
 * Plugin URI:        
 * Description:       Agrega Especialidades al sitio web La Pizzería (Custom Post Types, Meta Box)
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Juan Carlos Jiménez Gutiérrez
 * Author URI:        https://github.com/jcarlosj
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
*/

if( ! defined( 'ABSPATH' ) )  exit;     //  Evita acceso al codigo del plugin

add_action( 'init', 'lapizzeria_add_cpt_specialties' );

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

add_action( 'add_meta_boxes', 'lapizzeria_add_meta_boxes' );

/** Crear un MetaBox */
function lapizzeria_add_meta_boxes() {

    add_meta_box( 
        'lapizzeria-price',                                 #   ID unico de identificacion
        _x( 'Additional Information', 'lapizzeria' ),       #   Titulo para el Metabox
        'lapizzeria_meta_box_html_price',                   #   Callback: Funcion que dibujará formulario para el Metabox
        array( 'specialties' ),                             #   Nombre del Post o los Post a los que se agregará el Metabox
        'normal',                                           #   Contexto dentro de la pantalla donde debe mostrarse el cuadro: 'normal', 'side', and 'advanced'. Valor por defecto: 'advanced'
        'high',                                             #   La prioridad dentro del contexto donde debe mostrarse el cuadro: 'high', 'core', 'default', or 'low'. Valor por defecto: 'default'
        null                                                #   Datos que deben establecerse como la propiedad $ args de la matriz de caja (que es el segundo parámetro que se pasa a su devolución de llamada). Valor por defecto: null
    );
}

/** Despliega los campos que tendra el metabox en su formulario */
function lapizzeria_meta_box_html_price( $current_post ) {
    
    #   Agrega un nonce a un formulario
    wp_nonce_field( 
        basename( __FILE__ ),       #   Nombre del archivo actual
        'mb_nonce_specialities'     #   Nombre temporal para el formulario
    );

    $value = get_post_meta( 
        $current_post -> ID,    #   ID del Post
        'price',                #   Nombre del campo 
        true                    #   Si se debe devolver un solo valor. Este parámetro no tiene ningún efecto si no se especifica $key. Valor predeterminado: falso
    );
    $value = $value != '' ? $value : 0;

    ?>  
        <p><?php _e( 'Register the sale price of this specialty', 'lapizzeria' ); ?></p>
        <label for="price"><?php _e( 'Price', 'lapizzeria' ); ?></label>
        <input 
            id="price" 
            type="number" 
            name="price" 
            placeholder="<?php _e( 'Price for this specialty', 'lapizzeria' ); ?>" 
            min="0"
            value="<?php echo $value; ?>"
        />
    <?php
}

/** Guardar datos de un MetaBox */
function lapizzeria_save_meta_boxes( $post_id, $current_post, $update ) {

    // #   Verifica si NO puede validar el nonce del formulario
    if( ! isset( $_POST[ 'mb_nonce_specialities' ] ) || ! wp_verify_nonce( $_POST[ 'mb_nonce_specialities' ], basename( __FILE__ ) ) ) {
        return $post_id;
    }

    #   Verifica si NO puede el usuario editar el post
    if( ! current_user_can( 'edit_post', $post_id ) ) {
        return $post_id;
    }

    #   Verifica si esta definido el DOING_AUTOSAVE
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }

    #   Define variable con un valor por defecto
    $price = 0;

    #   Verifica que el valor de la variable obtenida del formulario esta definida
    if( isset( $_POST[ 'price' ] ) ) {
        $price = abs( $_POST[ 'price' ] );
    }
    update_post_meta( 
        $post_id,   #   ID del Post
        'price',    #   Nombre del campo
        $price      #   Valor a actualizar
    );
    
}
add_action( 'save_post', 'lapizzeria_save_meta_boxes', 10, 3 );

