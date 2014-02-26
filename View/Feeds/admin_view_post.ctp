<?php
App::import('Vendor', 'FunctionsVendor');
$functions = new FunctionsVendor();

extract($post);
//echo $this->Html->link(__('Volver', true), array('controller' => 'feeds', 'action' => 'view', $Post['blog_id']));

echo '<h1>' . $Post['title'] . '</h1>';

echo '<p style="border-top:solid 1px #333; border-bottom:solid 1px #333;">Por ' . $Post['author'] . ', ' . $functions->timeago($Post['date']) . '</p>';

echo '<img src="' . $Post['image'] . '" align="left" style="margin-right: 10px; margin-bottom: 1px;" />';

echo '<p style="text-align: justify;">' . $Post['description'] . '</p>';

echo '<div style="clear:both;"></div>';

if (!empty($Post['url'])) {
	echo '<p><a target="_blank" href="' . $Post['url'] . '">Ver post original</a></p>';
}

echo '<p>' . $this->Html->link('Eliminar', array('admin' => true, 'controller' => 'feeds', 'action' => 'delete_post', $Post['id']), array('confirm' => __('Seguro?'))) .'</p>';

echo '<div style="background-color:#CCC;height:1px; margin:30px 0px;"></div>';

echo $this->Form->create('Post', array('url' => array('controller' => 'feeds', 'action' => 'edit_post', $Post['id'])));

echo $this->Form->inputs(array(
	'fieldset' => false,
	'title' => array(
		'placeholder' => __('Título', true),
		'label' => __('Título', true),
	),
	'author' => array(
		'placeholder' => __('Autor', true),
		'label' => __('Autor', true),
	),
	'description' => array(
		'placeholder' => __('Descripción', true),
		'label' => __('Descripción', true),
	),
	'rewrite' => array(
		'label' => __('Sobreescribir', true),
		'type' => 'checkbox'
	),
));

echo $this->Form->submit(__('Actualizar', true));
echo $this->Html->link(__('cancelar', true), '/feeds');
echo $this->Form->end();
