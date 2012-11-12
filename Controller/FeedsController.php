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
			if (!$page) {
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

			$return = array();
			foreach ($feeds as $post) {
				//$return[] = $post['Post'];
				extract($post);
				$return[] = array(
					'id' => $Post['id'],
					'blog_id' => $Post['blog_id'],
					'title' => $Post['title'],
					'date' => $this->timeago($Post['date']),
					'author' => $Post['author'],
					'description' => $Post['description'],
					'image' => $Post['image'],
					'md5' => md5($Post['image']),
					'url' => 'http://www.familyblog.es/feeds/view_post/' . $Post['id'],
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

	function timeago($datefrom, $dateto = -1) {
		if ($datefrom <= 0) {
			return "Hace mucho tiempo";
		}
		if($dateto==-1) {
			$dateto = time();
		}
		$difference = $dateto - $datefrom;
		if($difference < 60) {
			$interval = "s";
		}
		elseif($difference >= 60 && $difference < 60*60) {
			$interval = "n";
		}
		elseif($difference >= 60*60 && $difference < 60*60*24) {
			$interval = "h";
		}
		elseif($difference >= 60*60*24 && $difference < 60*60*24*7) {
			$interval = "d";
		}
		elseif($difference >= 60*60*24*7 && $difference < 60*60*24*30) {
			$interval = "ww";
		}
		elseif($difference >= 60*60*24*30 && $difference < 60*60*24*365) {
			$interval = "m";
		}
		elseif($difference >= 60*60*24*365) {
			$interval = "y";
		}
		switch($interval) {
			case "m":
				$months_difference = floor($difference / 60 / 60 / 24 / 29);
				while (mktime(date("H", $datefrom), date("i", $datefrom),
							date("s", $datefrom), date("n", $datefrom) + ($months_difference),
							date("j", $dateto), date("Y", $datefrom)) < $dateto) {
					$months_difference++;
				}
				$datediff = $months_difference;
				if($datediff == 12) {
					$datediff--;
				}
				$res = ($datediff==1) ? "hace $datediff mes" : "hace $datediff meses";
				break;
			case "y":
				$datediff = floor($difference / 60 / 60 / 24 / 365);
				$res = ($datediff==1) ? "hace $datediff año" : "hace $datediff años";
				break;
			case "d":
				$datediff = floor($difference / 60 / 60 / 24);
				$res = ($datediff==1) ? "hace $datediff día" : "hace $datediff días";
				break;
			case "ww":
				$datediff = floor($difference / 60 / 60 / 24 / 7);
				$res = ($datediff==1) ? "la semana pasada" : "hace $datediff semanas";
				break;
			case "h":
				$datediff = floor($difference / 60 / 60);
				$res = ($datediff==1) ? "hace $datediff hora" : "hace $datediff horas";
				break;
			case "n":
				$datediff = floor($difference / 60);
				$res = ($datediff==1) ? "hace $datediff minuto" : "hace $datediff minutos";
				break;
			case "s":
				$datediff = $difference;
				$res = ($datediff==1) ? "hace $datediff segundo" : "hace $datediff segundos";
				break;
		}
		return $res;
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
