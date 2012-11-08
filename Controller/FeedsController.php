<?php
class FeedsController extends AppController {

	var $name = 'Feeds';

	function add($id = null) {
		if ($this->data) {
			if ($id) {
				$this->Feed->id = $id;
			} else {
				$this->Feed->create();
			}
			$this->Feed->save($this->data);
			return $this->redirect('/');
		}

		if ($id) {
			$feed = $this->Feed->findById($id);
			$this->data = $feed;
		}

		$this->set(compact('id'));
	}

	function delete($id = null) {
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

	function prueba() {}

	function feed() {
		if ($this->data) {
			$page = $this->data['page'];
			if (!$page) {
				$page = 1;
			}
			$device_id = $this->data['device_id'];

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

			$return = array();
			foreach ($feeds as $post) {
				$return[] = $post['Post'];
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
				'active' => 1
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

					if ($entry->children($namespaces['content'])->encoded) {
						$description = strip_tags((string)$entry->children($namespaces['content'])->encoded);
					} elseif (!empty($entry->description)) {
						$description = strip_tags((string)$entry->description);
					} else {
						$description = '';
					}

					$to_save = array(
						'id' => $Feed['id'] . strtotime($entry->pubDate),
						'blog_id' => $Feed['id'],
						'title' => str_replace("'", "\'", (string)$entry->title),
						'date' => strtotime($entry->pubDate),
						'author' => str_replace("'", "\'", (string)strip_tags($entry->children($namespaces['dc'])->creator)),
						'description' => str_replace("'", "\'", $description),
						'image' => (string)$image,
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

}
