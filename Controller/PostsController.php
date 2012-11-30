<?php
class PostsController extends AppController {

	public $layout = 'article';
	
	function view($id) {
		
		extract($this->Post->findById($id));

		$this->set(compact('Post'));

	}

	function qr() {
		$device = null;
		if (stristr($_SERVER['HTTP_USER_AGENT'], 'ipad')) {
			$device = "ipad";
		} elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'iphone') || strstr($_SERVER['HTTP_USER_AGENT'], 'iphone')) {
			$device = "iphone";
		} elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'blackberry')) {
			$device = "blackberry";
		} elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'android')) {
			$device = "android";
		}

		switch ($device) {
			case 'ipad':
			case 'iphone':
				$this->redirect('https://itunes.apple.com/us/app/family-blog-tu-revista-bloggers/id577736520?l=es&ls=1&mt=8');
				break;
			case 'android':
				$this->redirect('https://play.google.com/store/apps/details?id=net.artvisual.bloggers');
				break;
			default:
				$this->redirect('/');
				break;
		}
		die;
	}
}
