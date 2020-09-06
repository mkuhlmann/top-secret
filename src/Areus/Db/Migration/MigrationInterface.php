<?php

namespace Areus\Db\Migration;

interface MigrationInterface {
	public function up() : void;
	public function down() : void;
}
