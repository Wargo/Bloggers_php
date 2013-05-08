<?php
$feeds = ClassRegistry::init('Feed')->find('all', array('order' => array('prio' => 'asc')));

echo '<ul>';
foreach ($feeds as $feed) {
	extract($feed);
	if ($Feed['active'] == 0) {
		$style = 'text-decoration:line-through;';
	} else {
		$style = '';
	}
	echo '<li style="margin:5px;' . $style . '">';
		if (file_exists(WWW_ROOT . 'img/feeds/' . $Feed['id'] . '.png')) {
			echo $this->Html->image('feeds/' . $Feed['id'] . '.png', array('align' => 'absmiddle', 'style' => 'margin:0px 5px;', 'width' => 50));
		} else {
			echo $this->Html->image('logo.png', array('align' => 'absmiddle', 'style' => 'margin:0px 5px;', 'width' => 50));
		}
		echo $Feed['prio'] . ' ' . $this->Html->link($Feed['name'], array('controller' => 'feeds', 'action' => 'view', $Feed['id']));
		echo ' - ';
		echo __('Plus') . ': ' . $Feed['plus'];
		echo ' - ';
		echo $this->Html->link(__('editar', true), array('controller' => 'feeds', 'action' => 'edit', $Feed['id']));
		echo ' - ';
		echo $this->Html->link(__('borrar', true),
			array('controller' => 'feeds', 'action' => 'delete', $Feed['id']), array(), __('¿Seguro?', true));
	echo '</li>';
}
echo '</ul>';

echo $this->Html->link(__('Insertar nuevo blog', true), array('controller' => 'feeds', 'action' => 'edit'));
