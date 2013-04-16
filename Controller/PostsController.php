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

	function sitemap() {
		
		$this->layout = false;

		$posts = $this->Post->find('all');
		
		$this->set(compact('posts'));

		$write = '<?xml version=\'1.0\' encoding=\'UTF-8\'?>';
		$write .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';

		foreach ($posts as $post) {
			extract($post);
			$write .= '<url>
				<loc>http://www.familyblog.es/posts/view/' . $Post['id'] . '</loc>
				<changefreq>weekly</changefreq>
			</url>';
		}
		$write .= '</urlset>';

		$file = fopen(WWW_ROOT . 'sitemap.xml', 'w');
		fwrite($file, $write);
		fclose($file);

	}
}
