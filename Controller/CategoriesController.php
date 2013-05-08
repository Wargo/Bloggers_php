<?php
class CategoriesController extends AppController {

	var $layout = 'editor';

	function admin_index() {

		$categories = $this->Category->find('all', array('order' => array('order' => 'asc')));

		$this->set(compact('categories'));

	}

	function admin_edit($id = null) {

		if ($this->request->data) {

			if ($id) {
				$this->Category->id = $id;
			} else {
				$this->Category->create();
			}

			$this->Category->save($this->request->data);

			return $this->redirect('index');

		}

		if ($id) {
			$this->request->data = $this->Category->findById($id);
		}

		$feeds = ClassRegistry::init('Feed')->find('list', array('order' => array('prio' => 'asc')));

		$this->set(compact('feeds'));

	}

	function admin_delete($id = null) {
		if ($id) {
			$this->Category->delete($id);
		}
		return $this->redirect('index');
	}

}
