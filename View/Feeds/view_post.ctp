<?php
App::import('Vendor', 'FunctionsVendor');
$functions = new FunctionsVendor();

extract($post);
//echo $this->Html->link(__('Volver', true), array('controller' => 'feeds', 'action' => 'view', $Post['blog_id']));

echo '<h1>' . $Post['title'] . '</h1>';

echo '<p style="border-top:solid 1px #333; border-bottom:solid 1px #333;">Por ' . $Post['author'] . ', ' . $functions->timeago($Post['date']) . '</p>';

echo '<img src="' . $Post['image'] . '" align="left" style="margin-right: 10px; margin-bottom: 1px;" />';

echo '<p style="text-align: justify;">' . $Post['description'] . '</p>';

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
