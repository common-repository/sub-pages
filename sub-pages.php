<?php

/*
Plugin Name: Sub-Pages
Plugin URI: http://gamedefender.com/wp/index.php/tag/sub-pages/
Description: Simple Sub-page widget
Author: Alexander Sapountzis
Version: 0.3
Author URI: http://www.gamedefender.com/

Requirements:
* User entry for Title
* Title should be derived from parent_id if blank
* list subpages or siblings of current page
* do not list siblings of top level pages

Changes:
* (bug fix) Conflicts with other widgets I've created (fixed)

*/

function sub_pages_widget_header($title = "Browse") {
?>
  <li id="sub-pages" class="widget widget_sub-pages">
	  <h2 class="widgettitle"><?=$title ?></h2>
	  <ul><li><?
}

function sub_pages_widget_footer(){
	  ?>
  </li></ul>
      
  </li>
<?php
}

function sub_pages_get_my_pages($page_id = 0){
	return wp_list_pages("title_li=&child_of=$page_id&echo=0");
}

function sub_pages_widget_control(){

	$options = get_option("widget_sub_pages");
	if(!is_array($options)){
		$options = array(
			'title' => get_the_title($parent_id)
			);
	}
	
	if($_POST['sub-pages_widget_submit']){
		$options['title'] = htmlspecialchars($_POST['sub-pages_widget_title']);
		update_option("widget_sub_pages", $options);
	}

	?>
		<p>
		 <label for="sub-pages_widget_title">Title: </label>
		 <input type="text" id="sub-pages_widget_title" name="sub-pages_widget_title" value="<?=$options['title'] ?>" />
		 <input type="hidden" id="sub-pages_widget_submit" name="sub-pages_widget_submit" value="1" />
		</p>
		<p>
		 By default, title is set to the <em>parent's title</em>
		</p>
	<?
}

function widget_sub_pages($args) {

	extract($args);

	$options = get_option("widget_sub_pages");
	if(!is_array($options) || $options['title'] == ""){
		$options = array(
			'title' => get_the_title($parent_id)
			);
	}

	$id = get_the_ID();
	$parent_id = get_post($id)->post_parent;

	if($parent_id){
		$children = sub_pages_get_my_pages($parent_id);
	} else {
		$children = sub_pages_get_my_pages($id);
	}
			
	if($children) {
//		widget_header($parent_id);
		sub_pages_widget_header($options['title']);
		echo $children;
		sub_pages_widget_footer();
	}
	
}

function init_sub_pages()
{
  register_sidebar_widget(__('Sub-Pages'), 'widget_sub_pages');
  register_widget_control(	"Sub-Pages", 'sub_pages_widget_control');
}
add_action("plugins_loaded", "init_sub_pages");

?>