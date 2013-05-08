<?php
echo $this->Html->link(__('volver'), array('action' => 'index'));

echo $this->Form->create();

echo $this->Form->inputs(array(
	'name' => array(
		'label' => __('Nombre'),
	),
	'active' => array(
		'label' => __('Pública'),
	),
	'description' => array(
		'label' => __('Descripción'),
	),
	'order' => array(
		'label' => __('Orden'),
	),
	'Feed' => array(
		'label' => __('Blogs'),
		'multiple' => 'checkbox',
		'options' => $feeds
	),
));

echo $this->Form->submit(__('Guardar'));
echo $this->Form->end();
