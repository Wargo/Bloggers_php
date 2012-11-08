<?php
echo $this->Form->create(null, array('url' => array('controller' => 'feeds', 'action' => 'feed')));
?>
<input name="device_id" value="5871521" />
<input name="page" value="1" />
<?php
echo $this->Form->end('go');
