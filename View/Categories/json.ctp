<?php

$return = array();

foreach ($categories as $category) {

	extract($category);

	$blogs = ClassRegistry::init('CategoriesFeed')->find('count', array('conditions' => array('category_id' => $Category['id'])));

	$return[] = array(
		'id' => $Category['id'],
		'name' => $Category['name'],
		'description' => $Category['description'],
		'num' => sprintf(__n('%s blog', '%s blogs', $blogs), $blogs),
	);

}

echo json_encode(array('status' => 'ok', 'data' => $return));
