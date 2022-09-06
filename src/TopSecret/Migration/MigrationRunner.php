<?php

declare(strict_types=1);

namespace TopSecret\Migration;

use Areus\Db\MigrationManager;
use Areus\Db\MigrationRunnerInterface;

class MigrationRunner implements MigrationRunnerInterface
{

	private $availableMigrations = [
		Migration_20200430_Initial::class
	];

	private $db;

	public function __construct(\ParagonIE\EasyDB\EasyDB $db)
	{
		$this->db = $db;
	}

	public function migrate()
	{
		$manager = new MigrationManager($this, $this->db->getPdo(), $this->availableMigrations);
		$manager->migrate();
	}

	public function run($migrationClass): void
	{
		$migration = new $migrationClass($this->db);
		$migration->up();
	}
}
