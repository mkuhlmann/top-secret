<?php

namespace Areus\Db\Migration;

use PDO;

class MigrationManager {

	private $pdo;
	private $availableMigrations;

	private $migrationTableName = '_migrations';

	/**
	 * @param MigrationInterface[] $availableMigrations
	 */
	public function __construct(PDO $pdo, array $availableMigrations)
	{
		$this->pdo = $pdo;
		$this->availableMigrations = $availableMigrations;
	}

	public function migrate() {
		$migrations = $this->getPendingMigrations();

		foreach($migrations as $migration) {
			$this->runMigration($migration);
		}
	}

	public function getPendingMigrations() {
		$this->ensureMigrationTable();
		$migrations = $this->availableMigrations;
		$currentMigration = $this->getCurrentMigration();

		while($migrations[0] != $currentMigration) {
			array_shift($migrations);
		}
		return $migrations;
	}

	public function runMigration(MigrationInterface $migrationClass) {
		$migration = new $migrationClass();
		$migration->up($this->pdo);

		$statement = $this->pdo->prepare("INSERT INTO {$this->migrationTableName} (name, class_name, created_t) VALUES (:name, :class_name, :unix)");
		$statement->execute([
			':class_name' => $migrationClass,
			':name' => (new \ReflectionClass($migration))->getShortName(),
			':created_t' => time()
		]);
	}

	private function getCurrentMigration() {
		$result = $this->pdo->query("SELECT * FROM {$this->migrationTableName} ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
		return $result['name'];
	}


	private function ensureMigrationTable() {
		if(!$this->tableExists($this->pod, $this->migrationTableName)) {
			$this->pdo->exec("CREATE TABLE IF NOT EXISTS {$this->migrationTableName} (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(255) NOT NULL, class_name VARCHAR(255) NOT NULL, created_t INTEGER NOT NULL)");
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
	private function tableExists($pdo, $table) {
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
