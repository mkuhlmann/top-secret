<?php

namespace Areus\Db;

use ParagonIE\EasyDB\EasyStatement;

class QueryBuilder extends EasyStatement
{
	public static function open(): QueryBuilder
	{
		return new static();
	}

	public function where(string $column, $value): self
	{
		return $this->with($column . ' = ?', $value);
	}

	public function orWhere(string $column, $value): self
	{
		return $this->orWith($column . ' = ?', $value);
	}

	public function andWhere(string $column, $value): self
	{
		return $this->andWith($column . ' = ?', $value);
	}
}
