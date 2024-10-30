<?php
/*
 * Plugin Name: LH Add ID Columns
 * Plugin URI: https://lhero.org/portfolio/lh-add-id-columns/
 * Description: Adds sortable ID columns to the wordpress Post and Users, and multisite Users pages in wp-admin
 * Version: 1.03
 * Author: Peter Shaw
 * Author URI: https://shawfactor.com/
 * Text Domain: lh_add_id_columns
 * Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
* LH Add ID Columns plugin class
*/


if (!class_exists('LH_add_id_columns_plugin')) {


class LH_add_id_columns_plugin {
    
    private static $instance;
    
    static function return_plugin_namespace(){

    return 'lh_add_id_columns';

    }



    public function add_user_id_column($columns) {
    
        $columns =  $columns + array('user_id' => 'User ID');
        
        return $columns;
    
    }

public function add_user_id_sortable_column($sortable_columns){
  $sortable_columns['user_id'] = 'ID';
  return $sortable_columns;
}
 
public function show_user_id_column_content($value, $column_name, $user_id) {
    $user = get_userdata( $user_id );
	if ( 'user_id' == $column_name )
		return $user_id;
    return $value;
}
  




// ADD NEW COLUMN

public function add_post_id_column($columns){
	
$columns['post_id'] = __('Post ID', self::return_plugin_namespace());

return $columns;

}


// ADD NEW COLUMN

public function add_post_id_column_for_media($columns){

unset($columns['date']);
	
$columns['post_id'] = __('Post ID', self::return_plugin_namespace());

$columns['date'] = 'Date';

return $columns;

}

public function add_post_id_sortable_column($sortable_columns){
  $sortable_columns['post_id'] = 'ID';
  return $sortable_columns;
}
 
// SHOW THE POST ID
public function show_post_id_column_content($column_name, $post_ID) {
    if ($column_name == 'post_id') {
echo $post_ID;
    }
} 


// ADD NEW COLUMN

public function add_term_id_column($columns){

$columns['term_id'] = __('Term ID', self::return_plugin_namespace());

	return $columns;

}


// SHOW THE POST ID
public function show_term_id_column_content( $content, $column_name, $term_id ){
    
    
    if ( 'term_id' == $column_name ) {
        
        if (ob_get_status()){
           
           echo $term_id;
            
        } else {
            
            $content = $term_id;
            
        }
        
      
    }
	return $content;
}


public function add_comment_column( $columns ){
	$columns['my_custom_column'] = __( 'Comment ID', self::return_plugin_namespace() );
	return $columns;
}

public function comment_column_content( $column, $comment_ID ){
	if ( 'my_custom_column' == $column ) {
		echo $comment_ID;
	}
}

public function add_bp_group_id_column( $columns ) {
     
    $columns['group_id'] = __('Group ID', self::return_plugin_namespace());
     
    return $columns;
 
}

public function bp_group_id_column_content($value, $column_name, $item){
    
    if ($column_name == 'group_id'){
        
        $value = $item['id'];
        
    }
    
    return $value;
    
}

    public function hide_columns_by_default( $hidden, $screen ) {
    
        $hidden[] = 'post_id';

        
        return $hidden;
    
    }

public function plugin_init(){
    
    load_plugin_textdomain( self::return_plugin_namespace(), false, basename( dirname( __FILE__ ) ) . '/languages' ); 

add_filter('manage_users_columns', array($this,"add_user_id_column"));
add_filter('manage_users_sortable_columns', array($this,"add_user_id_sortable_column"));
add_action('manage_users_custom_column',  array($this,"show_user_id_column_content"), 10, 3);




add_filter('wpmu_users_columns', array($this,"add_user_id_column"));
add_action('wpmu_users_custom_column',  array($this,"show_user_id_column_content"), 10, 3);
add_filter('manage_users-network_sortable_columns', array($this,"add_user_id_sortable_column"),10,1);


add_filter('manage_posts_columns', array($this,"add_post_id_column"));
add_filter('manage_edit-post_sortable_columns', array($this,"add_post_id_sortable_column"));
add_action('manage_posts_custom_column', array($this,"show_post_id_column_content"), 10, 2);

add_filter( 'manage_upload_columns', array($this,"add_post_id_column_for_media"));
add_filter( 'manage_upload_sortable_columns', array($this,"add_post_id_sortable_column"));
add_action( 'manage_media_custom_column', array($this,"show_post_id_column_content"), 10, 2);

add_filter('manage_pages_columns', array($this,"add_post_id_column"));
add_filter('manage_edit-page_sortable_columns', array($this,"add_post_id_sortable_column"));
add_action('manage_pages_custom_column', array($this,"show_post_id_column_content"), 10, 2);


$args = array(
   'show_ui' => true,
'_builtin' => false
);

$output = 'names'; // names or objects, note names is the default
$operator = 'and'; // 'and' or 'or'


$post_types = get_post_types( $args, $output, $operator ); 

foreach ( $post_types  as $post_type ) {

add_filter('manage_'.$post_type.'_posts_columns', array($this,"add_post_id_column"));
add_filter('manage_edit-'.$post_type.'_sortable_columns', array($this,"add_post_id_sortable_column"));
//add_action('manage_'.$post_type.'_custom_column', array($this,"show_post_id_column_content"), 10, 2);


}

$taxonomies = get_taxonomies(); 
foreach ( $taxonomies as $taxonomy ) {
    
add_filter('manage_edit-'.$taxonomy.'_columns', array($this,'add_term_id_column'),10,1);

add_action('manage_'.$taxonomy.'_custom_column', array($this,'show_term_id_column_content'), 10, 3);
    
}


//add the ids to the comments table
add_filter( 'manage_edit-comments_columns',  array($this,"add_comment_column") );
add_filter( 'manage_comments_custom_column', array($this,"comment_column_content"), 10, 2 );


//buddypress groups
add_filter( 'bp_groups_list_table_get_columns', array($this,'add_bp_group_id_column'), 10, 1  );
add_filter( 'bp_groups_admin_get_group_custom_column', array($this,'bp_group_id_column_content'), 10, 3 );


add_filter( 'default_hidden_columns', array($this,'hide_columns_by_default'), 10, 2 );


}

	
	    /**
     * Gets an instance of our plugin.
     *
     * using the singleton pattern
     */
    public static function get_instance(){
        if (null === self::$instance) {
            self::$instance = new self();
        }
 
        return self::$instance;
    }



public function __construct() {

add_action( 'admin_init', array($this, 'plugin_init'));

}

}




$lh_add_id_columns_instance = LH_add_id_columns_plugin::get_instance();

}


?>