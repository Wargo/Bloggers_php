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
<img src="<?php echo $Post['image']; ?>" class="image left" alt="<?php echo $Post['title']; ?>" />
<p><?php echo nl2br($Post['description']); ?></p>
