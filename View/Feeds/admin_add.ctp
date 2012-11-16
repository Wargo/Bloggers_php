<?php

echo $this->Form->create('Feed', array('url' => array('controller' => 'feeds', 'action' => 'edit', $id)));

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
	'active' => array(
		'label' => __('Activo', true),
		'type' => 'checkbox'
	),
));

echo $this->Form->submit(__('Guardar', true));
echo $this->Html->link(__('cancelar', true), '/feeds');
echo $this->Form->end();
