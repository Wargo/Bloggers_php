<?php

echo $this->Form->create('Feed', array('type' => 'file', 'url' => array('controller' => 'feeds', 'action' => 'edit', $id)));

echo $this->Form->inputs(array(
	'fieldset' => false,
	'name' => array(
		'placeholder' => __('Nombre', true),
		'label' => __('Nombre', true),
	),
	'url' => array(
		'placeholder' => __('URL', true),
		'label' => __('URL', true),
	),
	'description' => array(
		'placeholder' => __('Descripción', true),
		'label' => __('Descripción', true),
	),
	'prio' => array(
		'label' => __('Orden', true),
	),
	'plus' => array(
		'label' => __('Tarifa (1 = destacado, 2 = icono + grande, 3 = ponerlo de los primeros)', true),
	),
	'active' => array(
		'label' => __('Activo', true),
		'type' => 'checkbox'
	),
	'logo' => array(
		'label' => __('Logo'),
		'type' => 'file',
	),
	'Category' => array(
		'label' => __('Categoría'),
		'multiple' => 'checkbox',
		'options' => $categories,
	),
));

if ($id) {
	echo $this->Html->image('feeds/' . $id . '.png');
}

echo $this->Form->submit(__('Guardar', true));
echo $this->Html->link(__('cancelar', true), '/feeds');
echo $this->Form->end();
