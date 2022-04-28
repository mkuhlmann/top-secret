<?php

namespace Areus\Db\Migration;

use PDO;

interface MigrationInterface {
	public function up(PDO $pdo);
	public function down(PDO $pdo);
}
