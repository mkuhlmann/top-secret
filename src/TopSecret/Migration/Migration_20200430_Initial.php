<?php

namespace TopSecret\Migration;

use Areus\Db\Migration\MigrationInterface;

use PDO;

class Migration_20200430_Initial implements MigrationInterface {
	
	public function up(PDO $pdo) {
		$this->pdo->exec('CREATE TABLE items (id INTEGER PRIMARY KEY AUTOINCREMENT, slug TEXT UNIQUE NOT NULL, title TEXT, name TEXT, path TEXT, size INTEGER, mime TEXT, extension TEXT, type TEXT, hits INTEGER, image_w INTEGER, image_h, INTEGER, json TEXT, created_t INTEGER, updated_t INTEGER)');
		$this->pdo->exec('CREATE TABLE tags (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, color TEXT)');
		$this->pdo->exec('CREATE TABLE items_tags (id INTEGER PRIMARY KEY AUTOINCREMENT, tag_id INTEGER NOT NULL, item_id INTEGER NOT NULL)');
	}

	public function down(PDO $pdo) {
		$this->pdo->exec('DROP TABLE items');
		$this->pdo->exec('DROP TABLE tags');
		$this->pdo->exec('DROP TABLE items_tags');
	}
}
