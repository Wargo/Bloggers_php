<?php
$feeds = ClassRegistry::init('Feed')->find('all');

echo '<ul>';
foreach ($feeds as $feed) {
	extract($feed);
	echo '<li>';
		echo $this->Html->link($Feed['name'], array('controller' => 'feeds', 'action' => 'view', $Feed['id']));
		echo ' - ';
		echo $this->Html->link(__('editar', true), array('controller' => 'feeds', 'action' => 'add', $Feed['id']));
		echo ' - ';
		echo $this->Html->link(__('borrar', true),
			array('controller' => 'feeds', 'action' => 'delete', $Feed['id']), array(), __('¿Seguro?', true));
	echo '</li>';
}
echo '</ul>';

echo $this->Html->link(__('Insertar nuevo blog', true), array('controller' => 'feeds', 'action' => 'add'));
