<?php
class CronShell extends AppShell {

	public function main() {
		ClassRegistry::init('Feed')->cron();
	}

}
