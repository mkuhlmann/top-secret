<?php

namespace TopSecret\Migration;

use Areus\Db\Migration\MigrationManager;

class MigrationRunner {

	private $availableMigrations = [
		Migration_20200430_Initial::class
	];

	private $db;

	public function __construct(\ParagonIE\EasyDB\EasyDB $db)
	{
		$this->db = $db;
	}

	public function migrate() {
		$manager = new MigrationManager($this->db->getPdo(), $this->availableMigrations);
		$manager->migrate();
	}
}
