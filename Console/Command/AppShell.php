<?php
class AppShell extends Shell {

	protected $_version = '1.0';

	public function startup() {
		$this->_name = strtolower(str_replace('Shell', '', $this->name));
	}

	public function main () {
		$this->help();
	}

	public function help () {
		$exclude = array('main');
		$shell = get_class_methods('Shell');
		$methods = get_class_methods($this);
		$methods = array_diff($methods, $shell);
		$methods = array_diff($methods, $exclude);

		$this->out($this->name . ' Shell. Version ' . $this->_version);


		foreach ($methods as $method) {
			if (!isset($help[$method]) && $method[0] !== '_') {
				$help[] = $method;
			}
		}
		$help = array_unique($help);
		sort($help);

		if (!$help) {
			$help[] = 'example';
			$help[] = 'another';
		}
		$this->out('');
		$this->out('Any project task which doesn\'t require a browser can be put here');
		$this->out('');
		foreach($help as $i => $message) {
			if (!$i) {
				$this->out("Usage: cake {$this->_name} $message <options> <args>");
			} else {
				$this->out("  or   cake {$this->_name} $message <options> <args>");
			}
		}
		$this->hr();
	}
}
