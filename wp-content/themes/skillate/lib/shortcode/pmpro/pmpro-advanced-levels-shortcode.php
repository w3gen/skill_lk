<?php

global $pmproal_link_arguments;
$pmproal_link_arguments = array();

$path = dirname(__FILE__);
require_once($path . "/templates/levels.php");


function pmproal_getLevelLandingPage($level_id) {
	if(is_object($level_id))
		$level_id = $level_id->id;

	$args = array(
		'post_type' => apply_filters('pmproal_level_landing_page_post_types', array('page', 'post')),
		'meta_query' => array(
			array(
				'key' => '_pmproal_landing_page_level',
				'value' => $level_id,
			)
		)
	);

	$posts = get_posts($args);

	if(empty($posts))
		return false;
	else
		return $posts[0];
}
