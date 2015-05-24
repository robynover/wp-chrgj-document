<?php
/*
Plugin Name: Post Type for Document
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: Defines a post type for documents on chrgj.org
Version: 1.0
Author: Robyn Overstreet
Author URI: http://robynoverstreet.com
License: GPL2
*/
//Constants
define('DOCUMENT_URLPATH', trailingslashit( WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) ) );

add_action('admin_print_scripts', 'document_javascript',1);
add_action('init', 'document_register');
// Add custom taxonomy
add_action( 'init', 'doc_create_taxonomies',2);

function document_register() {
 
	$labels = array(
		'name' => _x('Documents', 'post type general name'),
		'singular_name' => _x('Document', 'post type singular name'),
		'add_new' => _x('Add New', 'Document'),
		'add_new_item' => __('Add New Document'),
		'edit_item' => __('Edit Document'),
		'new_item' => __('New Document'),
		'view_item' => __('View Document'),
		'search_items' => __('Search Documents'),
		'not_found' =>  __('Nothing found'),
		'not_found_in_trash' => __('Nothing found in Trash'),
		'parent_item_colon' => ''
	);
 
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'documents'),
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => 5,
		'show_in_menu' => true,
		'has_archive' => true,
		'supports' => array('title','editor','thumbnail','category'),
		'taxonomies' => array('documenttype','category')
	  ); 
 
	register_post_type( 'chrgj_document',$args);
}
/**********************************
3 = Metaboxes
**********************************/
add_action("admin_init", "chrgj_documents_mb_create");
add_action('save_post', 'chrgj_documents_mb_save');

function chrgj_documents_mb_create(){
	add_meta_box( 'chrgj_documents_mb_url', 'PDF', 'document_url', 'chrgj_document', 'normal', 'high' );
	//add_meta_box( 'chrgj_documents_mb_newssource', 'Source', 'document_newssource', 'chrgj_document', 'normal', 'high' );
	//add_meta_box('chrgj_documents_mb_author', 'Author','document_author','chrgj_document','normal','high');
	add_meta_box('chrgj_documents_mb_pubdate', 'Publication Date','document_pubdate','chrgj_document','normal','high');
}

function chrgj_documents_mb_save(){
	global $post;
	update_post_meta($post->ID, 'document_url', $_POST['document_url']);
	//update_post_meta($post->ID, 'journals_description', $_POST['journals_description']);
	//update_post_meta($post->ID, 'document_newssource', $_POST['document_newssource']);
	//update_post_meta($post->ID, 'document_author', $_POST['document_author']);
	update_post_meta($post->ID, 'document_pubdate', $_POST['document_pubdate']);
}

/****************
URL
****************/
function document_url(){
	global $post;
	$custom = get_post_custom($post->ID);
	$document_url = $custom["document_url"][0];
	?>
	<input type="text" name="document_url" id="document_url" class="text" size="64" tabindex="1" value="<?php echo $document_url; ?>" />
	<p>If this document has an associated PDF file, link to it here.</p>
<?php
}
/****************
Source
****************/
/*function document_newssource(){
	global $post;
	$custom = get_post_custom($post->ID);
	$document_newssource = $custom["document_newssource"][0];
	?>
	<input type="text" name="document_newssource" id="document_newssource" class="text" size="64" value="<?php echo $document_newssource; ?>" />
	<p>The source of the document.</p>
<?php
}*/
/****************
Author
****************/
/*
function document_author(){
	global $post;
	$custom = get_post_custom($post->ID);
	$document_author = $custom["document_author"][0];
	?>
	
	<input type="text" name="document_author" id="document_author" class="text" size="64" value="<?php echo $document_author; ?>" />
	<p>The author of the document.</p>
<?php
}*/

/****************
Publication Date
****************/
function document_pubdate(){
	global $post;
	$custom = get_post_custom($post->ID);
	$document_pubdate = $custom["document_pubdate"][0];
	?>
	
	<input type="text" name="document_pubdate" id="document_pubdate" class="regular-date datepicker" size="64" value="<?php echo $document_pubdate; ?>" />
	<p>The publication date of the document, in the format YYYY-mm-dd. For example 2012-03-28</p>
<?php
}

/****************
javascript
****************/
function document_javascript() {

	global $post;
	
	if(isset($post->post_type)) {
	
		if($post->post_type == 'chrgj_document') {
		
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-datepicker', 'http://jquery-ui.googlecode.com/svn/trunk/ui/jquery.ui.datepicker.js', array('jquery', 'jquery-ui-core' ) );
		    //wp_enqueue_script('run-dp', DOCUMENT_URLPATH.'js/run_datepicker.js',array('jquery'));
		}
	}
}
//the script to call datepicker needs to be at the bottom
// so it doesn't interfere with WP's admin jQuery
//add_action( 'wp_print_scripts', 'doc_print_scripts');
// see external file "run_datepicker.js"

//css
add_action('admin_print_styles', 'document_css');
function document_css() {

	global $post;
	
	if(isset($post->post_type)) {	
		if($post->post_type == 'chrgj_document') {
			wp_enqueue_style( 'jquery-ui-css', DOCUMENT_URLPATH . 'css/datepicker.css');
		}
	}
}
/****************
Custom Taxonomy: Document types
****************/

function doc_create_taxonomies() {
	$labels = array(
	    'name' => _x( 'Document Types', 'taxonomy general name' ),
	    'singular_name' => _x( 'Document Type', 'taxonomy singular name' ),
	    'search_items' =>  __( 'Search Document Types' ),
	    'all_items' => __( 'All Document Types' ),
	    'parent_item' => __( 'Parent Document' ),
	    'parent_item_colon' => __( 'Parent Document:' ),
	    'edit_item' => __( 'Edit Document Type' ), 
	    'update_item' => __( 'Update Document Type' ),
	    'add_new_item' => __( 'Add New Document Type' ),
	    'new_item_name' => __( 'New Document Type Name' ),
	    'menu_name' => __( 'Document Type' ),
	  ); 	
	
	// Document type
    register_taxonomy('documenttype',array('chrgj-document'),array(
        'hierarchical' => true,
        'labels' => $labels,
        'singular_name' => 'Document Type',
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'document-type' ),
		'capabilities' => array('manage_categories'.'edit_posts')
    ));
}

//show the count on the dashboard
function my_right_now() {
    $num_widgets = wp_count_posts( 'chrgj_document' );

    $num = number_format_i18n( $num_widgets->publish );
    $text = _n( 'Document', 'Documents', $num_widgets->publish );
    if ( current_user_can( 'edit_pages' ) ) { 
        $num = "<a href='edit.php?post_type=chrgj_document'>$num</a>";
        $text = "<a href='edit.php?post_type=chrgj_document'>$text</a>";
    }   

    echo '<tr>';
    echo '<td class="first b b_pages">' . $num . '</td>';
    echo '<td class="t pages">' . $text . '</td>';
    echo '</tr>';
}
add_action( 'right_now_content_table_end', 'my_right_now' );

//manage date archives urls
add_action('generate_rewrite_rules', 'doc_datearchives_rewrite_rules');
 
function doc_datearchives_rewrite_rules($wp_rewrite) {
  $rules = doc_generate_date_archives('chrgj_document', $wp_rewrite);
  $wp_rewrite->rules = $rules + $wp_rewrite->rules;
  return $wp_rewrite;
}
function doc_generate_date_archives($cpt, $wp_rewrite) {
  $rules = array();
 
  $slug_archive = 'documents';
 
  $dates = array(
            array(
              'rule' => "([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})",
              'vars' => array('year', 'monthnum', 'day')),
            array(
              'rule' => "([0-9]{4})/([0-9]{1,2})",
              'vars' => array('year', 'monthnum')),
            array(
              'rule' => "([0-9]{4})",
              'vars' => array('year'))
        );
 
  foreach ($dates as $data) {
    $query = 'index.php?post_type='.$cpt;
    $rule = $slug_archive.'/'.$data['rule'];
 
    $i = 1;
    foreach ($data['vars'] as $var) {
      $query.= '&'.$var.'='.$wp_rewrite->preg_index($i);
      $i++;
    }
 
    $rules[$rule."/?$"] = $query;
    $rules[$rule."/feed/(feed|rdf|rss|rss2|atom)/?$"] = $query."&feed=".$wp_rewrite->preg_index($i);
    $rules[$rule."/(feed|rdf|rss|rss2|atom)/?$"] = $query."&feed=".$wp_rewrite->preg_index($i);
    $rules[$rule."/page/([0-9]{1,})/?$"] = $query."&paged=".$wp_rewrite->preg_index($i);
  }
 
  return $rules;
}

