<?php
extract($feed);

echo $this->Html->link(__('Volver', true), '/feeds');

echo '<br />';
echo '<br />';

echo $Feed['name'];

echo '<ul>';
foreach ($posts as $post) {
	extract($post);
	echo '<li>';
		echo $this->Html->link($Post['title'], array('controller' => 'feeds', 'action' => 'view_post', $Post['id']));
	echo '</li>';
}
echo '</ul>';
