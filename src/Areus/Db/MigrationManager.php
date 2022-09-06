<?php

declare(strict_types=1);

namespace Areus\Db;

use App\Db\Migrations\MigrationRunner;
use PDO;

class MigrationManager
{
	private $pdo;
	private $availableMigrations;
	private $migrationRunner;

	private $migrationTableName = '_migrations';

	public function __construct(MigrationRunnerInterface $runner, PDO $pdo, array $availableMigrations)
	{
		$this->migrationRunner = $runner;
		$this->pdo = $pdo;
		$this->availableMigrations = $availableMigrations;
	}

	public function migrate()
	{
		$migrations = $this->getPendingMigrations();

		foreach ($migrations as $migration) {
			$this->runMigration($migration);
		}
	}

	public function getPendingMigrations()
	{
		$this->ensureMigrationTable();
		$migrations = $this->availableMigrations;
		$currentMigration = $this->getCurrentMigration();
		while ($currentMigration && count($migrations) > 0 && $migrations[0] != $currentMigration) {
			array_shift($migrations);
		}
		return $migrations;
	}

	public function runMigration($migrationClass)
	{
		$this->migrationRunner->run($migrationClass);

		$statement = $this->pdo->prepare("INSERT INTO {$this->migrationTableName} (name, class_name, created_unix) VALUES (:name, :class_name, :unix)");
		$statement->execute([
			':class_name' => $migrationClass,
			':name' => (new \ReflectionClass($migrationClass))->getShortName(),
			':unix' => time()
		]);
	}

	private function getCurrentMigration()
	{
		$result = $this->pdo->query("SELECT * FROM {$this->migrationTableName} ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
		return $result ?? $result['name'];
	}


	private function ensureMigrationTable()
	{
		if (!$this->tableExists($this->pdo, $this->migrationTableName)) {
			$this->pdo->exec("CREATE TABLE IF NOT EXISTS {$this->migrationTableName} (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(255) NOT NULL, class_name VARCHAR(255) NOT NULL, created_unix INTEGER NOT NULL)");
		}
	}

	/**
	 * Set the value of migrationTableName
	 *
	 * @return self
	 */
	public function setMigrationTableName($migrationTableName)
	{
		$this->migrationTableName = $migrationTableName;
		return $this;
	}

	/**
	 * Check if a table exists in the current database.
	 * https://stackoverflow.com/a/14355475
	 *
	 * @param PDO $pdo PDO instance connected to a database.
	 * @param string $table Table to search for.
	 * @return bool TRUE if table exists, FALSE if no table found.
	 */
	private function tableExists($pdo, $table)
	{
		// Try a select statement against the table
		// Run it in try/catch in case PDO is in ERRMODE_EXCEPTION.
		try {
			$result = $pdo->query("SELECT 1 FROM $table LIMIT 1");
		} catch (\Exception $e) {
			// We got an exception == table not found
			return FALSE;
		}

		// Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
		return $result !== FALSE;
	}
}
