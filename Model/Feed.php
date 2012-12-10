<?php
class Feed extends AppModel {

	function cron() {

		$feeds = $this->find('all', array(
			'conditions' => array(
				//'active' => 1,
				'id' => 44
			),
			'fields' => array('id', 'url')
		));

		$this->Post = ClassRegistry::init('Post');


		foreach ($feeds as $feed) {
			
			extract($feed);

			$x = simplexml_load_file($Feed['url'], 'SimpleXMLElement', LIBXML_NOCDATA);

			if (!empty($x->channel)) {
				$elements = $x->channel->item;
			} else {
				$elements = $x->entry;
			}

			foreach ($elements as $entry) {

				$namespaces = $entry->getNameSpaces(true);

				if (!empty($namespaces['content']) && $entry->children($namespaces['content'])->encoded) {
					$description = (string)$entry->children($namespaces['content'])->encoded;
				} elseif (!empty($entry->description)) {
					$description = (string)$entry->description;
				} elseif (!empty($entry->summary)) {
					$description = (string)$entry->summary;
				} else {
					$description = '';
				}


				$image = null;

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
				if (!$image && !empty($namespaces['media'])) {
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
				if (!$image && !empty($description)) {
					$doc = new DOMDocument();
					$doc->loadHTML($description);
					$xml = simplexml_import_dom($doc);
					$images = $xml->xpath('//img');

					foreach ($images as $img) {
						if (strpos($img['src'], 'wordpress.com/b.gif')) {
							continue;
						}
						if ($img['width'] == 1 || $img['height'] == 1) {
							continue;
						}
						$image = $img['src'];
						break;
					}
				}
				if (!empty($entry->id)) {
					$id = explode('-', $entry->id);
					$id = $id[count($id) - 1];
				} else {
					$id = $Feed['id'] . strtotime($entry->pubDate);
				}

				/*
				 * Autor
				 */
				if (!empty($namespaces['dc'])) {
					$author = $this->clear(strip_tags($entry->children($namespaces['dc'])->creator));
				} elseif (!empty($entry->author)) {
					$author = (array)$entry->author;
					if (!empty($author['name'])) {
						$author = $author['name'];
					} else {
						$author = '';
					}
				} else {
					$author = '';
				}

				/*
				 * Date
				 */
				if (!empty($entry->pubDate)) {
					$date = strtotime($entry->pubDate);
				} elseif(!empty($entry->updated)) {
					$date = strtotime($entry->updated);
				} else {
					$date = '';
				}

				/*
				 * Enlace
				 */
				if (!empty($entry->link)) {
					$url = $entry->link;
				} else {
					foreach ($entry->link as $k => $v) {
						foreach ($v->attributes() as $key => $value) {
							if  ($key == 'href') {
								$url = (string)$value;
							}
						}
					}
				}

				$to_save = array(
					'id' => $id,
					'blog_id' => $Feed['id'],
					'title' => $this->clear($entry->title),
					'date' => $date,
					'author' => $author,
					'description' => !empty($description) ? $this->clear($description) : $this->clear($entry->content),
					'image' => utf8_decode((string)$image),
					'url' => $url,
				);

				if ($post = $this->Post->findById($id)) {
					if ($post['Post']['rewrite'] == 0) {
						//debug('No guardado ' . $to_save['title']);
						continue;
					}
				}

				$this->Post->save($to_save);
				//debug('Guardado ' . $to_save['title']);

			}

		}
		exec('chmod -R 777 /var/www/vhosts/familyblog.es/www/apps/live/tmp');
		die;
	}

	function clear($text) {
		$array1 = array('<br />', '&amp;', '&nbsp;', '&aacute;', '&Aacute;', '&eacute;', '&Eacute;', '&iacute;', '&Iacute;', '&oacute;', '&Oacute;', '&uacute;', '&Uacute;', '&ntilde;', '&Ntilde;');
		$array2 = array("\r\n", '&', ' ', 'á', 'Á', 'é', 'É', 'í', 'Í', 'ó', 'Ó', 'ú', 'Ú', 'ñ', 'Ñ');
		$text = str_replace($array1, $array2, $text);

		$array1 = array('&#8230;', '&#8594;', '&#8220;', '&#8230;', '&#8221;', '&#039;', '&#160;', '&#8216;', '&#8217;');
		$array2 = array('', '', '', '', '', '', '', "'", "'");
		$text = str_replace($array1, $array2, $text);
		//$text= preg_replace('/&#(\d+);/me',"chr(\\1)",$text);
		return strip_tags(trim((string)$text));
	}

}
