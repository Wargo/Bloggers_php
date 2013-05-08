<ul class="menu">
	<li><?php echo $this->Html->link(__('Blogs'), array('admin' => true, 'controller' => 'feeds', 'action' => 'index'), array('class' => $this->params['controller'] == 'feeds' ? 'selected' : '')); ?></li>
	<li><?php echo $this->Html->link(__('Categorías'), array('admin' => true, 'controller' => 'categories', 'action' => 'index'), array('class' => $this->params['controller'] == 'categories' ? 'selected' : '')); ?></li>
</ul>
