<?php
class PostsController extends AppController {

	public $layout = 'article';
	
	function view($id) {
		
		extract($this->Post->findById($id));

		$this->set(compact('Post'));

	}

}
