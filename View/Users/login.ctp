<?php

echo '<h2>' . __('Login') . '</h2>';

echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'login')));

echo $this->Form->inputs(array(
	'fieldset' => false,
	'username',
	'password' => array('type' => 'password')
));

echo $this->Form->submit(__('Login'));
echo $this->Form->end();

