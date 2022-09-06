<?php

namespace TopSecret\Migration;

use Areus\Db\MigrationInterface;
use ParagonIE\EasyDB\EasyDB;
use PDO;

class Migration_20200430_Initial implements MigrationInterface
{
	private EasyDB $db;

	public function __construct(EasyDB $db)
	{
		$this->db = $db;
	}


	public function up(): void
	{
		$this->db->exec('CREATE TABLE IF NOT EXISTS items (id INTEGER PRIMARY KEY AUTOINCREMENT, slug TEXT UNIQUE NOT NULL, title TEXT, name TEXT, path TEXT, size INTEGER, mime TEXT, extension TEXT, type TEXT, hits INTEGER, image_w INTEGER, image_h, INTEGER, json TEXT, created_t INTEGER, updated_t INTEGER)');
		$this->db->exec('CREATE TABLE IF NOT EXISTS tags (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, color TEXT)');
		$this->db->exec('CREATE TABLE IF NOT EXISTS items_tags (id INTEGER PRIMARY KEY AUTOINCREMENT, tag_id INTEGER NOT NULL, item_id INTEGER NOT NULL)');
	}

	public function down(): void
	{
		$this->db->exec('DROP TABLE items');
		$this->db->exec('DROP TABLE tags');
		$this->db->exec('DROP TABLE items_tags');
	}
}
