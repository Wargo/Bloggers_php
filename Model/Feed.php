<?php
class Feed extends AppModel {

	function cron() {

		$feeds = $this->find('all', array(
			'conditions' => array(
				//'active' => 1,
				//'id' => 9
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

				$image = null;

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

				if (!empty($entry->id)) {
					$id = explode('-', $entry->id);
					$id = $id[count($id) - 1];
				} else {
					$id = $Feed['id'] . strtotime($entry->pubDate);
				}

				$to_save = array(
					'id' => $id,
					'blog_id' => $Feed['id'],
					'title' => $this->clear($entry->title),
					'date' => strtotime($entry->pubDate),
					'author' => !empty($namespaces['dc']) ? $this->clear(strip_tags($entry->children($namespaces['dc'])->creator)) : '',
					'description' => !empty($description) ? $this->clear($description) : strip_tags($this->clear($entry->content)),
					'image' => utf8_decode((string)$image),
				);

				if ($post = $this->Post->findById($id)) {
					if ($post['Post']['rewrite'] == 0) {
						//$this->out('No guardado ' . $to_save['title']);
						debug('No guardado ' . $to_save['title']);
						continue;
					}
				}

				//$this->Post->id = $Feed['id'] . strtotime($entry->pubDate);
				$this->Post->save($to_save);
				//$this->out('Guardado ' . $to_save['title']);
				debug('Guardado ' . $to_save['title']);

			}

		}
		//chmod('/var/vhosts/www/familyblog.es/www/apps/live/tmp', 777);
		exec('chmod -R 777 /var/www/vhosts/familyblog.es/www/apps/live/tmp');
		die;
	}

	function clear($text) {
		$array1 = array('&nbsp;', '&aacute;', '&Aacute;', '&eacute;', '&Eacute;', '&iacute;', '&Iacute;', '&oacute;', '&Oacute;', '&uacute;', '&Uacute;', '&ntilde;', '&Ntilde;');
		$array2 = array(' ', 'á', 'Á', 'é', 'É', 'í', 'Í', 'ó', 'Ó', 'ú', 'Ú', 'ñ', 'Ñ');
		$text = str_replace($array1, $array2, $text);

		$array1 = array('&#8230;', '&#8594;', '&#8220;', '&#8230;', '&#8221;', '&#039;', '&#160;', '&#8217;');
		$array2 = array('', '', '', '', '', '', '', "'");
		$text = str_replace($array1, $array2, $text);
		//$text= preg_replace('/&#(\d+);/me',"chr(\\1)",$text);
		return trim((string)$text);
	}

}
