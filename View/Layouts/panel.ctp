<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $title_for_layout; ?>
	</title>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script> 
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('cake.generic');
		echo $this->fetch('css');

		echo $this->Html->script('js');
		echo $this->fetch('script');

		echo $this->fetch('meta');
	?>
</head>
<body>
	<div id="container">
		<div id="header">
			<h1><strong><?php echo __('Panel de control', true); ?></strong></h1>
			<ul class="menu">
				<li><?php echo $this->Html->link(__('Islas', true), array('admin' => true, 'controller' => 'islands', 'action' => 'index'), array('class' => $this->params['controller'] == 'islands' ? 'selected' : '')); ?></li>
				<li><?php echo $this->Html->link(__('Categorías', true), array('admin' => true, 'controller' => 'categories', 'action' => 'index'), array('class' => $this->params['controller'] == 'categories' ? 'selected' : '')); ?></li>
				<li><?php echo $this->Html->link(__('Sitios', true), array('admin' => true, 'controller' => 'sites', 'action' => 'index'), array('class' => $this->params['controller'] == 'sites' ? 'selected' : '')); ?></li>
				<li><?php echo $this->Html->link(__('Sobre...', true), array('admin' => true, 'controller' => 'texts', 'action' => 'index'), array('class' => $this->params['controller'] == 'texts' ? 'selected' : '')); ?></li>
			</ul>
		</div>
		<div id="content">
			<div class="bg_header"></div>
			<?php echo $this->Session->flash(); ?>
			<?php echo $this->fetch('content'); ?>
		</div>
		<div id="footer">
		</div>
	</div>
	<?php echo $this->element('sql_dump'); ?>
</body>
</html>
