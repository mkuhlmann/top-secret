<?php

declare(strict_types=1);

namespace Areus\Db;

interface MigrationRunnerInterface
{
	public function run($migrationClass): void;
}
