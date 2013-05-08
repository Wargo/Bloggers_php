<?php
class UsersController extends AppController {

	function login() {

		if ($this->request->data) {
			
			if (ClassRegistry::init('User')->find('first', array(
				'conditions' => array(
					'username' => $this->data['User']['username'],
					'password' => md5($this->data['User']['password']),
				)
			))) {
				$this->Session->write('admin', true);
				return $this->redirect('/');
			}
			
		}

	}

}
