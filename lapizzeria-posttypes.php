<?php
/**
 * Plugin Name:       La Pizzeria: Especialidades
 * Plugin URI:        
 * Description:       Agrega Especialidades al sitio web La Pizzería (Custom Post Types, Meta Box, Taxonomies)
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
		'taxonomies'         => array( '' ),
        'show_in_rest'       => true,               #   Habilita el despliege de los datos como una API
        'rest_base'          => 'api-specialties'   #   Crea el URL o endpoint de acceso a esta data
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
        'side',                                             #   Contexto dentro de la pantalla donde debe mostrarse el cuadro: 'normal', 'side', and 'advanced'. Valor por defecto: 'advanced'
        'default',                                          #   La prioridad dentro del contexto donde debe mostrarse el cuadro: 'high', 'core', 'default', or 'low'. Valor por defecto: 'default'
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


/** Registrar una Taxonomia */
function lapizzeria_add_taxonomy() {

	$labels = array(
        'name' =>           _x( 'Category Menu', 'taxonomy general name', 'lapizzeria' ),
        'singular_name'     => _x( 'Category Menu', 'taxonomy singular name', 'lapizzeria' ),
        'search_items'      => __( 'Search Category Menu', 'lapizzeria' ),
        'all_items'         => __( 'All Categories Menu', 'lapizzeria' ),
        'parent_item'       => __( 'Parent Menu Category', 'lapizzeria' ),
        'parent_item_colon' => __( 'Category Menu:', 'lapizzeria' ),
        'edit_item'         => __( 'Edit Category Menu', 'lapizzeria' ),
        'update_item'       => __( 'Update Category Menu', 'lapizzeria' ),
        'add_new_item'      => __( 'Add Category Menu', 'lapizzeria' ),
        'new_item_name'     => __( 'New Category Menu', 'lapizzeria' ),
        'menu_name'         => __( 'Category Menu', 'lapizzeria' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'lapizzeria-category-menu' ),
		'show_in_rest'	    => true,
		'rest_base'	    => 'lapizzeria-category-menu'
	);

    #   Registra la Taxonomia y la asocia a un Custom Post Type
	register_taxonomy( 'lapizzeria-category-menu', array( 'specialties' ), $args );     
}

add_action( 'init', 'lapizzeria_add_taxonomy', 0 );

/** Agrega campos al REST API */
function add_response_fields_to_rest_api() {

    /** Registra campos en un tipo de objeto de WordPress existente al API */
    register_rest_field(
        'specialties',          #   Nombre del Objeto (CPT Especialidades)   
        'price',                #   Nombre del atributo que se agregara (Meta Box del CTP)
        array(                  #   Matriz de argumentos que se utiliza para manejar el campo registrado
            'get_callback'      => 'lapizzeria_get_price',      #   Opcional. La función de devolución de llamada utilizada para recuperar el valor del campo. El valor predeterminado es 'null'.
            'update_callback'   =>  null,                       #   Opcional. La función de devolución de llamada utilizada para establecer y actualizar el valor del campo. El valor predeterminado es 'null'
            'schema'            =>  null                        #   Opcional. El esquema de este campo. El valor predeterminado es 'null'
        )
    );
    register_rest_field(
        'specialties',          #   Nombre del Objeto (CPT Especialidades)   
        'category_ids',         #   Nombre del atributo que se agregara (Categoria del CTP)
        array(                  #   Matriz de argumentos que se utiliza para manejar el campo registrado
            'get_callback'      => 'lapizzeria_get_taxonomy_ids',   #   Opcional. La función de devolución de llamada utilizada para recuperar el valor del campo. El valor predeterminado es 'null'.
            'update_callback'   =>  null,                           #   Opcional. La función de devolución de llamada utilizada para establecer y actualizar el valor del campo. El valor predeterminado es 'null'
            'schema'            =>  null                            #   Opcional. El esquema de este campo. El valor predeterminado es 'null'
        )
    );

}
add_action( 'rest_api_init', 'add_response_fields_to_rest_api' );

/** Obtiene el valor del meta box 'price' del CPT */
function lapizzeria_get_price() {

    #   Verifica si la funcion para obtener un campo NO esta habilitada
    if( ! function_exists( 'get_post_meta' ) ) {
        // wp_die( 'get_field no esta habilitado' );
        return;
    }

    #   Verifica si existe el campo requerido
    if( get_post_meta( get_the_ID(), 'price', true ) ) {

        $value = get_post_meta( 
            get_the_ID(),           #   ID del Post Actual (Specialties)
            'price',                #   Nombre del campo (Meta Box)
            true                    #   Si se debe devolver un solo valor. Este parámetro no tiene ningún efecto si no se especifica $key. Valor predeterminado: falso
        );

        #   Retorna el valor hacia el API Rest
        return intval( $value ); 
    }

    return false;
}

/** Obtiene los terminos registrados en la taxonomia del CPT */
function lapizzeria_get_taxonomy_ids() {
    global $post;

    $term_ids = [];
    $taxonomies = get_object_taxonomies( $post );
    $terms = get_the_terms( $post -> ID, $taxonomies );
    // var_dump( $terms );

    foreach ( $terms as $key => $term ) {
        // print_r( $terms[ $key ] -> term_id );
        $term_ids[] = $term -> term_id;
    }
    // var_dump( $term_ids );

    return $term_ids;
}