<?php
App::import('Vendor', 'FunctionsVendor');
$functions = new FunctionsVendor();
?>
<h4><?php echo $Post['title']; ?></h4>
<blockquote>
	<?php
	if (!empty($Post['author'])) {
		echo 'Por ' , $Post['author'] , ', ' , $functions->timeago($Post['date']);
	} else {
		echo ucfirst($functions->timeago($Post['date']));
	}
	?>
</blockquote>
<?php echo $this->Html->link('<img width="200" src="' . $Post['image'] . '" class="image left" alt="' . $Post['title'] . '" />', $Post['image'], array('escape' => false, 'target' => '_blank')); ?>
<p><?php echo nl2br($Post['description']); ?></p>
