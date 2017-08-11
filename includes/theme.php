<?php

function oqp_templates($template){
    global $oqp_form;

    if(!$oqp_form) return $template;

    if(oqp_is_single()){
            $template_file = 'single/oqp-single.php';
    }elseif(is_oqp_form()){
            $template_file = 'single/oqp-form.php';

    }elseif (oqp_is_directory()){
        $template_file = get_post_meta($oqp_form->page_id, '_wp_page_template', true);
        
        //use oqp archive template only if no page template has been defined
        if($template_file=='default')$template_file = 'oqp-archive.php';
    }
    
    if($template_file){
        $template = oqp_locate_theme_template($template_file);
        oqp_debug($template,"oqp_templates");
       
    }

    return $template;
    
    
}

function oqp_get_theme_file_url($filename,$filepath=false,$is_url=true) {
	if ($filepath)
		$filepath.='/';

	$url =  OQP_PLUGIN_URL . 'theme/'.$filepath.$filename;
	$path =  OQP_PLUGIN_DIR . 'theme/'.$filepath.$filename;

	if ( file_exists( $path ) ) {
		if ($is_url){
			$located = $url;
		}else{
			$located = $path;
		}
	}
	return apply_filters('oqp_get_theme_file',$located,$filename,$filepath,$is_url);
} 

function oqp_get_template_part( $slug, $name = null,$load=true) {
	do_action( "oqp_get_template_part{$slug}", $name );

	if ( isset($name) )
		$template = "{$slug}-{$name}.php";

	$template = "{$slug}.php";

	return oqp_locate_template($template, $load);
}

function oqp_locate_template( $template_name, $load = false ) {


	return oqp_locate_theme_template( $template_name, $load );
}

function oqp_locate_theme_template( $template_name, $load = false ) {

	$located = '';

        //CURRENT THEME PATH
        $theme_path = get_stylesheet_directory();
        $theme_file = $theme_path.'/'.OQP_SLUG.'/'.$template_name;

        if(file_exists($theme_file)){
            $located = $theme_file;

        }else{//OQP PATH
            $oqp_path = OQP_PLUGIN_DIR.'theme/'. $template_name;
            if ( file_exists( $oqp_path ) )$located = $oqp_path;
        }

        
        if(!$located) return false;

        $located = apply_filters('oqp_locate_theme_template',$located,$template_name);

	if ($load && '' != $located)
		load_template($located);

	return $located;
}


/**
 * Use this only inside of screen functions, etc (code snippet from MrMaz)
 *
 * @param string $template
 */
function oqp_load_template( $template ) {
	bp_core_load_template( $template );
}



function oqp_js_autocomplete($the_blog_id,$form_id,$form_field_id,$taxonomy_name) {
	global $blog_id;
	
	if (($the_blog_id) && ($the_blog_id!=$blog_id))
		$wpurl = get_blog_option($the_blog_id,'siteurl');
	else
		$wpurl = get_bloginfo('wpurl');


	$js[] = '<script type="text/javascript">';
	$js[] = '//<![CDATA[';
	$js[] = 'jQuery(document).ready( function($) {';
	$js[] = '	$("#'.$form_id.' #'.$form_field_id.'").autocomplete("'.$wpurl.'/wp-admin/admin-ajax.php?action=ajax-tag-search&tax='.$taxonomy_name.'", {';
	$js[] = '		width: jQuery(this).width,';
	$js[] = '		multiple: true,';
	$js[] = '		matchContains: true,';
	$js[] = '		minChars: 3,';
	$js[] = '	});';
	$js[] = '});';
	$js[] = '//]]>';
	$js[] = '</script>';
	return implode("\n",$js);
}

//returns normal EDIT POST url OR 
//custom frontend URL

function oqp_get_edit_post_link($admin_url,$post_id,$context) {
	if (!is_oqp_post($post_id)) return $admin_url;
	return oqp_get_edition_link(false,$post_id);
}
function oqp_get_delete_post_link($admin_url,$post_id,$context) {
    if(is_admin())return $admin_url;
    if (!is_oqp_post($post_id)) return $admin_url;
    return oqp_get_delete_link(false,$post_id);
}


function oqp_bp_only_sidebar(){
	locate_template( array( 'sidebar.php' ), true );
}
function oqp_wp_only_sidebar() {
	get_sidebar();
}




function oqp_wp_styles() {
    
    
        if(!is_oqp()) return false;
    
	//TO FIX use style.php instead
	//wp_enqueue_style( 'oqp', oqp_get_theme_file_url('style.php') );

	wp_enqueue_style( 'oqp', oqp_get_theme_file_url('shared.css','_inc/css') );
	
	if ( defined( 'BP_VERSION' )){
		wp_enqueue_style( 'oqp-bp',oqp_get_theme_file_url('screen-bp.css','_inc/css') );
	}else{
		wp_enqueue_style( 'oqp-wp', oqp_get_theme_file_url('screen-wp.css','_inc/css') );
	}
	
        do_action('oqp_wp_styles');
        
}

function oqp_wp_scripts() {
    
        if(!is_oqp()) return false;
    
	wp_enqueue_script( 'oqp',oqp_get_theme_file_url('oqp.js','_inc/js'),array('jquery'), OQP_VERSION );
	
	$localize_vars=array(
		'excerpt_link_text'=>__('Custom excerpt','oqp')
	);
        
        $localize_vars=apply_filters('oqp_localize_vars',$localize_vars);
        
        do_action('oqp_wp_scripts');

	wp_localize_script( 'oqp', 'oqp', $localize_vars);
        
        

}

function oqp_wp_footer(){
    
    if(!is_oqp()) return false;
    
    //we can add JS on this action hook
    do_action('oqp_wp_footer');
    
}



function oqp_fields_edit_only_enabled($enabled,$field) {
	global $oqp_form;

	if ($field->edit_only) return false;
	
	return $enabled;
}

function oqp_single_post_author_buttons() {
	?>
	<div class="oqp_buttons">
		<?php do_action('query_subscribe_favorite_link');?>
		<?php do_action('query_subscribe_author_link');?>
	</div>
	<?php
}


function oqp_post_review(){
    global $post;
    
    
    ?>
    <div role="complementary" id="item-header">
            <?php 
            if ($post->ID){
                    oqp_get_template_part('single/header', 'index' );
            }
            ?>
    </div>
    <?php
}



function oqp_tabs_admin_button(){
        global $post;
        
        $can_post_edit = current_user_can( 'edit_post', $post->ID );
        
        if(!$can_post_edit) return false;
        
        if(is_oqp_form())return false;
	

        $url_args['oqp_step']=oqp_get_step_slug();

        $url = oqp_get_edition_link($url_args);

	$post_label = strtolower(oqp_get_post_type_label());
	
	?>
		<li class="oqp_admin"><a href="<?php echo $url;?>"><?php printf(__('Admin %s', 'oqp'),$post_label);?></a></li>
	<?php
}

function oqp_tabs_viewpost_button(){
	global $oqp_form;
	if (!$oqp_form->loaded_post->ID) return false;
        
	
	$url = get_permalink($oqp_form->loaded_post->ID);

	$post_label = strtolower(oqp_get_post_type_label());
	
	?>
		<li class="oqp_admin"><a href="<?php echo $url;?>"><?php printf(__('View %s', 'oqp'),$post_label);?></a></li>
	<?php
}

function oqp_tabs_delete_button() {
	global $oqp_form;
        if(!is_oqp_form())return false;
	if (!$oqp_form->loaded_post->ID) return false;
	$cap_needed='delete_post';
	if (!oqp_user_can_for_ptype($cap_needed)) return false;
        $url=get_delete_post_link();
	?>
		<li class="oqp_admin"><a onclick="return confirm('<?php printf(__('Do you really want to delete this %s ?','oqp'),strtolower(oqp_get_post_type_label()));?>')" href="<?php echo $url;?>"><?php _e('Delete', 'oqp');?></a></li>
	<?php
}


//populates the form for a post written with OQP
function oqp_single_post_init_form() {
	global $post;
	global $oqp_form;

	if (!is_single()) return false;

	$is_oqp_post = is_oqp_post();
	if (!$is_oqp_post) return false; //this is not a post written with OQP
	
	$form_slug = oqp_post_get_form_page_id();
	$args['url']=get_permalink();
	//$oqp_form=new Oqp_Display_Post($form_slug,$args,$post->ID);

}




function oqp_add_main_buttons(){
	global $oqp_form;
	?>
	<div class="oqp_main_buttons">
		<?php
		if (!oqp_is_directory()) {
			oqp_directory_button();
		}
		oqp_creation_button();
		?>
	</div>
	<?php
}




function oqp_field_label_asterisk($label){
    global $oqp_form;
    
    if(!$label) return false;
    
    $field=$oqp_form->step->field;
    if (($field->edit)&&($field->required)) $label.='*';

    return $label;
}

function oqp_field_label_edit($label){
    global $oqp_form;
    
    if(is_admin())return $label;
    
    $field=$oqp_form->step->field;
    
    if(!$label) return false;
    //if (!$field->edit) return $label;

    $label.=' <small class="edit_action">['.__('edit','oqp').']</small>';

    return $label;
}

function oqp_body_class($classes){

    global $post,$oqp_form;
    $ptype=get_query_var('post_type');


    //if (!is_oqp()) return $classes;
    
    $classes[] = 'oqp';

    if(oqp_is_single()){
        $classes[] = 'singular-oqp';
    }elseif(is_oqp_form()){
        $classes[] = 'oqp-admin';
        $classes[] = 'singular-oqp';
        $remove_classes[]='singular-page-'.$oqp_form->page_id;
        $remove_classes[]='singular-page';
    }elseif (oqp_is_directory()){
        $classes[] = 'archive-oqp';
        $remove_classes[]='singular-'.$ptype.'-'.$post->ID;
        $remove_classes[]='singular-'.$ptype;
        $remove_classes[]='page-'.$post->ID;
        $remove_classes[]='singular';
    }
    
    if((is_oqp_form())||(oqp_is_directory())){
        $remove_classes[]='page-'.$post->ID;
    }
    
    if($remove_classes){
        foreach ($classes as $key=>$class){
            if(in_array($class,$remove_classes)) unset ($classes[$key]);
        }
    }
    
        
        
        return $classes;
}

function oqp_field_label(){
    global $oqp_form;
    $field=$oqp_form->step->field;

    $label = $field->get_label();
    if ($label){
        ?>
        <label for="<?php echo $fieldname;?>"><?php echo $label;?></label>
        <?php
    }
}
function oqp_field_description(){
    global $oqp_form;
    $field=$oqp_form->step->field;
    $description =  $field->get_description();
    if ($description)echo $description;
}
function oqp_field_notices(){
    global $oqp_form;
    $field=$oqp_form->step->field;
    $notices = $field->get_notice_html();
    if ($notices)echo $notices;
}

 
 function oqp_page_title( $title, $separator ){


        global $oqp_form;
        
	
        if(is_oqp_form()){
            if($oqp_form->loaded_post->ID){
                $title.=sprintf(__('Admin %s','oqp'),strtolower(oqp_get_post_type_label()));
            }else{
                $title.=sprintf(__('Create new %s','oqp'),strtolower(oqp_get_post_type_label()));
            }
            $title.=" ".$separator." ";
        }
        
        return $title;
        
        
 }
 
 /*
 * DIRECTORY TABS 
 */
 
 function oqp_count_posts($args=false){
     
     $args = array_merge(oqp_get_form_query(),(array)$args);
     
     $count_q = new WP_Query($args);
     return (int)$count_q->found_posts;
 }

function oqp_directory_tab_all($tabs_selected){

    if (!$tabs_selected)$classes[]='selected';
    
    if($classes)$class_str=' class="'.implode(' ',$classes).'"';
    
    $text =sprintf(__( 'All %s', 'oqp' ),strtolower(oqp_get_post_type_label('name')));
    //TO FIX
    $count = oqp_count_posts();
    $link=yclads_get_dir_link();
    
    ?>
    <li<?php echo $class_str;?> id="all">
        <a href="<?php echo $link; ?>"><?php echo $text;?><span class="count"><?php echo $count ?></span></a>
    </li>
    <?php
}

function oqp_directory_tab_search($tabs_selected){
    global $wp_query;
    if (!$tabs_selected['search']) return false;
    
    $classes[]='selected';
    if($classes)$class_str=' class="'.implode(' ',$classes).'"';

    $count = $wp_query->found_posts;
    $link= add_query_arg($wp_query->query_vars,oqp_form_page_get_link());
    $text = __('Search results','yclads');

    ?>
    <li<?php echo $class_str;?> id="search">
        <a href="<?php echo $link; ?>"><?php echo $text;?><span class="count"><?php echo $count ?></span></a>
    </li>
    <?php
}




function oqp_directory_tabs_self($tabs_selected){

    if (!is_user_logged_in()) return false;

    $count= oqp_count_posts( array('author'=>get_current_user_id()) );
    $link = oqp_get_user_posts_link(get_current_user_id());

    if(!$count) return false;
    
        if ($tabs_selected['author']==get_current_user_id())$classes[]='selected';
        if($classes)$class_str=' class="'.implode(' ',$classes).'"';
    
    ?>
    <li<?php echo $class_str;?> id="self"><a href="<?php echo $link; ?>"><?php printf(__( 'My %s', 'yclads' ),strtolower(oqp_get_post_type_label('name'))); ?><span class="count"><?php echo $count;?></span></a></li>
    <?php
}


function oqp_directory_tabs_selected(){
    global $wp_query;
    
    $query_args = $wp_query->query; //get args for this search
    unset($query_args['context']);
    $query_args = array_filter($query_args);
    
    $page_args = oqp_get_form_query();
    
    $search_args = array_diff((array)$query_args,(array)$page_args);

    if($query_args['s']){ //CUSTOM SEARCH
        $tabs_selected['search']=true;   
    }else{
        if($search_args['author']){ //SINGLE AUTHOR
            $tabs_selected['author']=$wp_query->get('author');
        }elseif($search_args){ //CUSTOM SEARCH
            $tabs_selected['search']=true;
        }else{//ALL SELECTED //TO CHECK ?
            $tabs_selected=false;
        }
    }
    $tabs_selected = apply_filters('oqp_directory_tabs_selected',$tabs_selected);
    oqp_debug($tabs_selected,"oqp_directory_tabs_selected");
    return $tabs_selected;
}




function oqp_directory_tabs(){
    if (!oqp_is_directory()) return false;
    $tabs_selected = oqp_directory_tabs_selected();
    
    //print_R($tabs_selected);

    
    ?>
    <div id="oqp-directory-tabs" class="item-list-tabs oqp-tabs" role="navigation">
         <?php do_action( 'oqp_before_directory_tabs' ); ?>
        <ul>
               
                <?php do_action( 'oqp_directory_tabs',$tabs_selected); ?>
               
        </ul>
         <?php do_action( 'oqp_after_directory_tabs' ); ?>
    </div><!-- .item-list-tabs -->
    <?php
}







function oqp_get_post_tab_name_single_upload_field($name,$step){
    
    if(is_admin())return false;
    
    if(!$step->fields)return $name;
    if(count($step->fields)>1)return $name;
    
    $field = $step->fields[0];
    if($field->model!='upload')return $name;

    return $name.'<span class="count">'.count($field->value).'</span>';
}




function oqp_do_theme_init(){
    
    
    
    //DIRECTORY TABS
    add_action('oqp_directory_tabs','oqp_directory_tab_all',3);
    add_action('oqp_directory_tabs','oqp_directory_tab_search',5);
    add_action('oqp_directory_tabs','oqp_directory_tabs_self',20);
    add_action('oqp_before_loop','oqp_directory_tabs',8);

    //ARCHIVE

    //add post date
    add_action('oqp_before_loop_item_metas','oqp_post_date');
    //add post status
    add_action('oqp_before_loop_item_header','oqp_post_status');

    //SINGLE
    //add post date
    add_action('oqp_before_single_item_header','oqp_post_date');
    //add post status
    add_action('oqp_before_single_item_header','oqp_post_status');

    //add visit count
    add_action('oqp_before_single_item_header','oqp_visit_count');

    //add author sidebar
    add_action('oqp_before_step','oqp_single_post_author_sidebar');

    //add subscribe button for author
    add_filter('oqp_single_post_author_infos_before','oqp_single_post_author_buttons',10,2);
    
    

    //add ?oqp=true when single template is not auto-loaded
    add_filter('the_permalink','oqp_single_post_permalink');

    //add field label
    add_action('oqp_before_field','oqp_field_label');
    //add field description
    add_action('oqp_before_field','oqp_field_description');
    //add field notices
    add_action('oqp_before_field','oqp_field_notices');

    add_filter('body_class','oqp_body_class',11);

    add_filter('oqp_field_label','oqp_field_label_asterisk');
    add_filter('oqp_field_label','oqp_field_label_edit');
    add_filter( 'wp_title', 'oqp_page_title', 10, 2 );

    //load styles
    add_action('wp_print_styles','oqp_wp_styles');

    //load scripts
    add_action('wp_print_styles','oqp_wp_scripts');
    
    

    //select OQP template
    add_filter('template_include', 'oqp_templates');
    
   

    //adds directory/creation buttons
    add_action( 'oqp_before_content', 'oqp_add_main_buttons',9);

    //footer; you can add scripts on action oqp_wp_footer
    add_action('wp_footer', 'oqp_wp_footer');

    //if a step contains only one field
    //and that this is an UPLOAD field
    //add file count
    add_filter('oqp_get_post_tab_name','oqp_get_post_tab_name_single_upload_field',10,2);
    
    
    //we already have the title in the preview template, so remove it when displaying a single post.
    add_action("populated_postdata","oqp_preview_remove_title");
    
    
    //gallery shortcode
    if ((is_singular) && (is_oqp())){
        //deactivate WordPress function
        remove_shortcode('gallery', 'gallery_shortcode');
        //activate own function
        add_shortcode('gallery', 'oqp_gallery_shortcode');
    }
    
}



function oqp_theme_init(){
    
    //IMAGE SIZES
    
    if ( function_exists( 'add_image_size' ) ) {
        
        add_theme_support( 'post-thumbnails' );

        $oqp_images = array(
            'oqp_large'=>array(
                'width'=>500,
                'height'=>500
            ),
            'oqp_normal'=>array(
                'width'=>200,
                'height'=>200
            ),
            'oqp_thumb_header'=>array(
                'width'=>150,
                'height'=>150,
                'crop'=>true
            ),
            'oqp_thumb'=>array(
                'width'=>50,
                'height'=>50,
                'crop'=>true
            )
        );
        
        $oqp_images = apply_filters('oqp_images_size',$oqp_images);
        
        foreach ((array)$oqp_images as $name=>$attr){
            add_image_size($name,$attr['width'],$attr['height'],$attr['crop']);
        }
    }
    
    

    //EXCERPT
    //remove default theme READY MORE; only for OQP posts so add the filter again for non OQP posts
    //TO FIX to check not very clean ?
    add_filter('the_excerpt', 'oqp_the_excerpt');

    
    add_action('oqp_single_post_author_infos_after','oqp_author_bio_warning');
    
	
}

//add extra tab AUTHOR
add_action('oqp_populated_steps','oqp_single_post_extra_tab_author');
//add extra tab COMMENTS
add_action('oqp_populated_steps','oqp_single_post_extra_tab_comments');


add_action( 'after_setup_theme', 'oqp_theme_init' );

add_action('oqp_has_init','oqp_do_theme_init');

?>