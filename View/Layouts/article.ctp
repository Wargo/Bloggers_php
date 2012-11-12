<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $Post['title']; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('styles');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
<body>
	<div class="wrapper">
		<?php echo $this->element('header'); ?>
		<?php echo $this->element('menu'); ?>
		<div class="nav-container">
			<?php echo $this->Session->flash(); ?>
			<?php echo $this->fetch('content'); ?>
		</div>
		<?php echo $this->element('footer'); ?>
	</div>
</body>
</html>
