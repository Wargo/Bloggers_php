<?php
class FeedsController extends AppController {

	var $name = 'Feeds';

	var $layout = 'editor';

	var $ips = array('84.123.66.33', '127.0.0.1');

	function edit($id = null) {
		if (!in_array($_SERVER['REMOTE_ADDR'], $this->ips)) {
			return $this->redirect('/');
		}
		if ($this->request->data) {
			if ($id) {
				$this->Feed->id = $id;
			} else {
				$this->Feed->create();
			}
			$this->Feed->save($this->request->data);
			return $this->redirect('/feeds');
		}

		if ($id) {
			$feed = $this->Feed->findById($id);
			$this->request->data = $feed;
		}

		$this->set(compact('id'));

		$this->render('admin_add');
	}

	function index() {
		if (!in_array($_SERVER['REMOTE_ADDR'], $this->ips)) {
			return $this->redirect('/');
		}
	}

	function delete($id = null) {
		if (!in_array($_SERVER['REMOTE_ADDR'], $this->ips)) {
			return $this->redirect('/');
		}
			
		if (empty($id)) {
			return $this->redirect('/feeds');
		}

		$this->Feed->delete($id);
		return $this->redirect('/feeds');
	}

	function view($id = null) {
		if (empty($id)) {
			return $this->redirect('/');
		}

		$feed = $this->Feed->findById($id);
		
		$posts = ClassRegistry::init('Post')->find('all', array(
			'conditions' => array(
				'blog_id' => $id
			),
		));

		$this->set(compact('feed', 'posts'));
	}

	function view_post($id = null) {
		if (!in_array($_SERVER['REMOTE_ADDR'], $this->ips)) {
			return $this->redirect('/');
		}
		if (empty($id)) {
			return $this->redirect('/');
		}
		
		$this->data = $post = ClassRegistry::init('Post')->findById($id);

		$this->set(compact('post'));
	}

	function edit_post($id = null) {
		if (!in_array($_SERVER['REMOTE_ADDR'], $this->ips)) {
			return $this->redirect('/');
		}
		if (empty($id)) {
			return $this->redirect('/');
		}

		if ($this->request->data) {
			$this->loadModel('Post');
			$this->Post->id = $id;
			$this->Post->save($this->data);
			return $this->redirect($this->referer());
		}
	}

	function havefavs($debug = false) {
		if ($debug) {
			$this->request->data['device_id'] = 4859254;
		}
		if ($this->request->data) {
			extract($this->request->data);
			$this->loadModel('Favourite');
			$num = $this->Favourite->find('count', array(
				'conditions' => array(
					'device_id' => $device_id,
				)
			));
			echo json_encode(array('data' => $num)); die;
		}
		die;
	}

	function prueba() {
		$this->layout = 'default';
	}

	function feed($debug = false) {
		if ($debug) {
			$this->request->data['device_id'] = 1;//4859254;
		}
		if ($this->request->data) {
			extract($this->request->data);
			if (empty($page)) {
				$page = 1;
			}

			$limit = 15;

			$this->loadModel('Preference');
			$this->loadModel('Post');

			$this->Preference->bindModel(array(
				'belongsTo' => array('Feed')
			));

			if ($device_id === 1) {
				$ids = $this->Preference->find('all', array(
					'conditions' => array(
						'active' => 1
					)
				));
			} else {
				$ids = $this->Preference->find('all', array(
					'conditions' => array(
						'device_id' => $device_id,
						'active' => 1
					)
				));
			}

			$ids = Set::extract('/Preference/feed_id', $ids);

			$feeds = $this->Post->find('all', array(
				'conditions' => array(
					'blog_id' => $ids,
				),
				'limit' => $device_id === 1 ? 0 : $limit,
				'offset' => ($page - 1) * $limit,
				'order' => 'date desc'
			));

			App::import('Vendor', 'FunctionsVendor');
			$functions = new FunctionsVendor();

			$return = array();
			foreach ($feeds as $post) {
				extract($post);
				
				if (strpos($Post['image'], 'blog.elemb')) {
					$path = 'http://elembarazo.net/wp-content/blogs.dir/9/files/';
					$image = explode('/', $Post['image']);
					$image = $path . $image[count($image) - 1];
					$image_big = str_replace('-150x150', '', $image);
				} else {
					$image = $Post['image'];
					$image_big = $Post['image'];
				}

				$blog = $this->Feed->findById($Post['blog_id']);

				if (file_exists('/var/www/vhosts/familyblog.es/www/apps/live/webroot/img/feeds/' . $Post['blog_id'] . '.png')) {
					$ico = 'http://www.familyblog.es/img/feeds/' . $Post['blog_id'] . '.png';
				} else {
					$ico = 'http://www.familyblog.es/img/feeds/0.png';
				}
				
				$return[] = array(
					'id' => $Post['id'],
					'blog_id' => $Post['blog_id'],
					'blog_name' => $blog['Feed']['name'],
					'blog_ico' => $ico,
					'title' => $Post['title'],
					'date' => $functions->timeago($Post['date']),
					'author' => $Post['author'],
					'description' => $Post['description'],
					'image' => $image,
					'image_big' => $image_big,
					'md5' => md5($Post['image']),
					'url' => 'http://www.familyblog.es/posts/view/' . $Post['id'],
					'original_url' => $Post['url'],
				);
			}

			echo json_encode(array('status' => 'ok', 'data' => $return));
			die;
		} else {
			echo json_encode(array('status' => 'ko', 'message' => 'No hemos recibido datos')); die;
		}
	}

	function add() {

		if ($this->request->data) {
			extract($this->request->data);

			$this->loadModel('Preference');
			$exists = $this->Preference->find('first', array(
				'conditions' => array(
					'device_id' => $device_id,
					'feed_id' => $feed_id
				),
			));

			if ($exists) {
				$this->Preference->deleteAll(array('device_id' => $device_id, 'feed_id' => $feed_id));
				echo json_encode(array('status' => 'ok', 'message' => 'Borrado')); die;
			} else {
				$this->Preference->create();
				$this->Preference->save(array(
					'device_id' => $device_id,
					'feed_id' => $feed_id,
				));
				echo json_encode(array('status' => 'ok', 'message' => 'Insertado')); die;
			}
		} else {
			echo json_encode(array('status' => 'ko', 'message' => 'error en los datos')); die;
		}

	}

	function feeds($debug = false) {
		if ($debug) {
			$this->request->data['device_id'] = 4859254;
		}
		if ($this->request->data) {
			extract($this->request->data);
			$feeds = $this->Feed->find('all', array(
				'conditions' => array(
					'active' => 1
				),
				'order' => array('prio' => 'asc')
			));

			$return = array();
			
			$this->loadModel('Preference');
			foreach ($feeds as $feed) {
				extract($feed);
				$haveIt = $this->Preference->find('first', array(
					'conditions' => array(
						'device_id' => $device_id,
						'feed_id' => $Feed['id'],
					),
				));

				if (file_exists(WWW_ROOT . 'img/feeds/' . $Feed['id'] . '.png')) {
					$image = 'http://www.familyblog.es/img/feeds/' . $Feed['id'] . '.png';
				} else {
					$image = 'http://www.familyblog.es/img/feeds/0.png';
				}

				$return[] = array(
					'id' => $Feed['id'],
					'name' => $Feed['name'],
					'image' => $image,
					'description' => $Feed['description'],
					'haveIt' => $haveIt?true:false,
					'plus' => $Feed['plus'],
				);
			}
			echo json_encode(array('status' => 'ok', 'data' => $return)); die;
		} else {
			echo json_encode(array('status' => 'ko', 'message' => 'error en los datos')); die;
		}

	}

	function isFavorite() {

		if ($this->request->data) {

			extract($this->request->data);

			$this->loadModel('Favourite');
			if ($this->Favourite->find('first', array(
					'conditions' => array(
						'device_id' => $device_id,
						'post_id' => $id
					),
				))) {
				echo json_encode(array('status' => 'ok', 'data' => 1)); die;
			} else {
				echo json_encode(array('status' => 'ok', 'data' => 0)); die;
			}

		} else {
			echo json_encode(array('status' => 'ko', 'message' => 'error en los datos')); die;
		}

	}

	function favourites() {

		if ($this->request->data) {

			extract($this->request->data);

			$this->loadModel('Favourite');

			if ($favourite = $this->Favourite->find('first', array(
					'conditions' => array(
						'device_id' => $device_id,
						'post_id' => $id,
						'blog_id' => $blog_id,
					),
				))) {
				$this->Favourite->delete($favourite['Favourite']['id']);
				echo json_encode(array('status' => 'ok', 'data' => 0)); die;
			} else {
				$this->Favourite->create();
				$this->Favourite->save(array(
					'device_id' => $device_id,
					'post_id' => $id,
					'blog_id' => $blog_id,
				));
				echo json_encode(array('status' => 'ok', 'data' => 1)); die;
			}

		} else {
			echo json_encode(array('status' => 'ko', 'message' => 'error en los datos')); die;
		}

	}

	function myposts() {

		$limit = 10;

		if ($this->request->data) {

			extract($this->request->data);

			$this->loadModel('Favourite');
			$this->loadModel('Post');

			$ids = $this->Favourite->find('all', array(
				'conditions' => array(
					'device_id' => $device_id
				),
			));

			$ids = Set::extract('/Favourite/post_id', $ids);

			$posts = $this->Post->find('all', array(
				'conditions' => array(
					'id' => $ids
				),
				'limit' => $limit,
				'offset' => ($page - 1) * $limit,
				'order' => 'date desc'
			));

			App::import('Vendor', 'FunctionsVendor');
			$functions = new FunctionsVendor();

			$return = array();

			foreach ($posts as $post) {
				extract($post);

				if (strpos($Post['image'], 'blog.elemb')) {
					$path = 'http://elembarazo.net/wp-content/blogs.dir/9/files/';
					$image = explode('/', $Post['image']);
					$image_big = $path . $image[count($image) - 1];
					$image = $path . str_replace('-150x150', '', $image[count($image) - 1]);
				} else {
					$image = $Post['image'];
					$image_big = $Post['image'];
				}

				$blog = $this->Feed->findById($Post['blog_id']);

				$return[] = array(
					'id' => $Post['id'],
					'blog_id' => $Post['blog_id'],
					'blog_name' => $blog['Feed']['name'],
					'blog_ico' => 'http://www.familyblog.es/img/feeds/' . $Post['blog_id'] . '.png',
					'title' => $Post['title'],
					'date' => $functions->timeago($Post['date']),
					'author' => $Post['author'],
					'description' => $Post['description'],
					'image' => $Post['image'],
					'image_big' => $image_big,
					'md5' => md5($Post['image']),
					'url' => 'http://www.familyblog.es/posts/view/' . $Post['id'],
					'original_url' => $Post['url'],
				);

			}

			echo json_encode(array('status' => 'ok', 'data' => $return)); die;

		} else {
			echo json_encode(array('status' => 'ko', 'message' => 'error en los datos')); die;
		}

	}

	function arreglar() {
		return;
		$this->loadModel('Post');
		$posts = $this->Post->find('all');

		$array1 = array('&#8230;', '&#8594;', '&#8220;', '&#8230;', '&#8221;');
		$array2 = array('', '', '', '', '');

		foreach ($posts as $post) {
			extract($post);
			$this->Post->id = $Post['id'];
			$fields = array('description'); //'title', 
			foreach ($fields as $field) {
				$Post[$field] = str_replace($array1, $array2, $Post[$field]);
			}
			$this->Post->save($Post);
		}
		die;
	}


}
