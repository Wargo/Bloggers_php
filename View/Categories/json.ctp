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
		'ico' => 'http://www.familyblog.es/img/categories/' . $Category['id'] . '.png?v=' . strtotime($Category['modified']),
	);

}

echo json_encode(array('status' => 'ok', 'data' => $return));
