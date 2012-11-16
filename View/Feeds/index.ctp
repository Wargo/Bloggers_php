<?php
$feeds = ClassRegistry::init('Feed')->find('all', array('order' => array('prio' => 'asc')));

echo '<ul>';
foreach ($feeds as $feed) {
	extract($feed);
	echo '<li>';
		echo $Feed['prio'] . ' ' . $this->Html->link($Feed['name'], array('controller' => 'feeds', 'action' => 'view', $Feed['id']));
		echo ' - ';
		echo $this->Html->link(__('editar', true), array('controller' => 'feeds', 'action' => 'edit', $Feed['id']));
		echo ' - ';
		echo $this->Html->link(__('borrar', true),
			array('controller' => 'feeds', 'action' => 'delete', $Feed['id']), array(), __('¿Seguro?', true));
	echo '</li>';
}
echo '</ul>';

echo $this->Html->link(__('Insertar nuevo blog', true), array('controller' => 'feeds', 'action' => 'edit'));
