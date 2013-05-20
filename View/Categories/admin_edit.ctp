<?php
echo $this->Html->link(__('volver'), array('action' => 'index'));

echo $this->Form->create('Category', array('type' => 'file'));

echo $this->Form->submit(__('Guardar'));

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
	'ico' => array(
		'label' => __('Imagen'),
		'type' => 'file',
	),
	'Feed' => array(
		'label' => __('Blogs'),
		'multiple' => 'checkbox',
		'options' => $feeds
	),
));

echo $this->Form->submit(__('Guardar'));
echo $this->Form->end();
