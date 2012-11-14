<?php
class FeedsController extends AppController {

	var $name = 'Feeds';

	function admin_add($id = null) {
		if ($this->request->data) {
			if ($id) {
				$this->Feed->id = $id;
			} else {
				$this->Feed->create();
			}
			$this->Feed->save($this->request->data);
			return $this->redirect('/');
		}

		if ($id) {
			$feed = $this->Feed->findById($id);
			$this->request->data = $feed;
		}

		$this->set(compact('id'));
	}

	function admin_delete($id = null) {
		if (empty($id)) {
			return $this->redirect('/');
		}

		$this->Feed->delete($id);
		return $this->redirect('/');
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
		if (empty($id)) {
			return $this->redirect('/');
		}
		
		$post = ClassRegistry::init('Post')->findById($id);

		$this->set(compact('post'));
	}

	function feed() {
		if ($this->request->data) {
			extract($this->request->data);
			if (empty($page)) {
				$page = 1;
			}

			$limit = 10;

			$this->loadModel('Preference');
			$this->loadModel('Post');

			$this->Preference->bindModel(array(
				'belongsTo' => array('Feed')
			));

			$ids = $this->Preference->find('all', array(
				'conditions' => array(
					'device_id' => $device_id,
					'active' => 1
				)
			));

			$ids = Set::extract('/Preference/feed_id', $ids);

			$feeds = $this->Post->find('all', array(
				'conditions' => array(
					'blog_id' => $ids,
				),
				'limit' => $limit,
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
					$image_big = $path . $image[count($image) - 1];
					$image = $path . str_replace('-150x150', '', $image[count($image) - 1]);

				} else {
					$image = $Post['image'];
				}
				
				$return[] = array(
					'id' => $Post['id'],
					'blog_id' => $Post['blog_id'],
					'title' => $Post['title'],
					'date' => $functions->timeago($Post['date']),
					'author' => $Post['author'],
					'description' => $Post['description'],
					'image' => $image,
					'image_big' => $image_big,
					'md5' => md5($Post['image']),
					'url' => 'http://www.familyblog.es/posts/view/' . $Post['id'],
				);
			}

			echo json_encode(array('status' => 'ok', 'data' => $return));
			die;
		} else {
			echo json_encode(array('status' => 'ko', 'message' => 'No hemos recibido datos')); die;
		}
	}

	function cron() {
		$feeds = $this->Feed->find('all', array(
			'conditions' => array(
				'active' => 1,
			),
			'fields' => array('id', 'url')
		));

		$this->loadModel('Post');

		foreach ($feeds as $feed) {
			
			extract($feed);

			$x = simplexml_load_file($Feed['url'], 'SimpleXMLElement', LIBXML_NOCDATA);

			foreach ($x->channel->item as $entry) {

				$image = null;

				try {

					$namespaces = $entry->getNameSpaces(true);

					if (substr($entry->enclosure['type'], 0, 5) == 'image') {
						$image = $entry->enclosure['url'];
					}
					if (!$image) {
						$getImage = false;
						if (!empty($namespaces['media'])) {
							foreach($entry->children($namespaces['media'])->content->attributes() as $key => $value) {
								if ($key == 'type' && substr($value, 0, 5) == 'image') {
									$getImage = true;
								}
								if ($key == 'url' && $getImage) {
									$image = $value;
									$getImage = false;
								}
							}
						}
					}
					if (!$image) {
						$getImage = true;
						foreach ($entry->children($namespaces['media']) as $key => $value) {
							foreach ($value->attributes() as $k => $v) {
								if ($k == 'url' && $getImage && strpos((string)$v, 'gravatar') === false) {
									$getImage = false;
									$image = (string)$v;
								}
							}
						}
					}
					if (!$image && !empty($entry->description)) {
						$doc = new DOMDocument();
						$doc->loadHTML($entry->description);
						$xml = simplexml_import_dom($doc);
						$images = $xml->xpath('//img');

						foreach ($images as $img) {
							$image = $img['src'];
							break;
						}
					}


					if (!empty($namespaces['content']) && $entry->children($namespaces['content'])->encoded) {
						$description = strip_tags((string)$entry->children($namespaces['content'])->encoded);
					} elseif (!empty($entry->description)) {
						$description = strip_tags((string)$entry->description);
					} else {
						$description = '';
					}

					$to_save = array(
						'id' => $Feed['id'] . strtotime($entry->pubDate),
						'blog_id' => $Feed['id'],
						'title' => $this->clear($entry->title),
						'date' => strtotime($entry->pubDate),
						'author' => !empty($namespaces['dc']) ? $this->clear(strip_tags($entry->children($namespaces['dc'])->creator)) : '',
						'description' => $this->clear($description),
						'image' => utf8_decode((string)$image),
					);

					//$this->Post->id = $Feed['id'] . strtotime($entry->pubDate);
					$this->Post->save($to_save);

				} catch (Exception $e) {
					echo json_encode(array('status' => 'ko', 'message' => 'error en los datos')); die;
				}

			}

		}
		die;
	}

	function clear($text) {
		/*
		$array1 = array('&nbsp;', '&aacute;', '&Aacute;', '&eacute;', '&Eacute;', '&iacute;', '&Iacute;', '&oacute;', '&Oacute;', '&uacute;', '&Uacute;', '&ntilde;', '&Ntilde;');
		$array2 = array(' ', 'á', 'Á', 'é', 'É', 'í', 'Í', 'ó', 'Ó', 'ú', 'Ú', 'ñ', 'Ñ');
		return (string)str_replace($array1, $array2, $text);
		*/
		return html_entity_decode((string)$text);
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

	function feeds() {

		if ($this->request->data) {
			extract($this->request->data);
			$feeds = $this->Feed->find('all', array(
				'conditions' => array(
					'active' => 1
				),
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

				$return[] = array(
					'id' => $Feed['id'],
					'name' => $Feed['name'],
					'image' => '/ui/images/feeds.png',
					'description' => $Feed['description'],
					'haveIt' => $haveIt?true:false
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

			$return = array();

			foreach ($posts as $post) {
				extract($post);

				$return[] = array(
					'id' => $Post['id'],
					'blog_id' => $Post['blog_id'],
					'title' => $Post['title'],
					'date' => $Post['date'],
					'author' => $Post['author'],
					'description' => $Post['description'],
					'image' => $Post['image'],
					'md5' => md5($Post['image']),
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
		$posts = $this->Post->find('all', array('conditions' => array('modified <=' => '2012-11-08 14:37:54')));

		foreach ($posts as $post) {
			extract($post);
			$this->Post->id = $Post['id'];
			$fields = array('description', 'image'); //'title', 
			foreach ($fields as $field) {
				$Post[$field] = utf8_decode($Post[$field]);
				$Post[$field] = html_entity_decode($Post[$field]);
			}
			$this->Post->save($Post);
		}
		die;
	}


}
