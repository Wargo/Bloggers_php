<?php
echo '<ul>';
foreach ($categories as $category) {
	extract($category);
	echo '<li>';
		echo $Category['order'] . ' ' . $this->Html->link($Category['name'], array('controller' => 'feeds', 'action' => 'view', $Category['id']));
		echo ' - ';
		echo $this->Html->link(__('editar', true), array('controller' => 'categories', 'action' => 'edit', $Category['id']));
		echo ' - ';
		echo $this->Html->link(__('borrar', true),
			array('controller' => 'categories', 'action' => 'delete', $Category['id']), array(), __('¿Seguro?', true));
	echo '</li>';
}
echo '</ul>';

echo $this->Html->link(__('Insertar nueva categoria', true), array('controller' => 'categories', 'action' => 'edit'));
