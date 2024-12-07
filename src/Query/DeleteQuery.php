<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query;

use Citrus\Database\QueryPack;
use Citrus\Query\Where\Expression;

/**
 * Delete Query Builder
 */
class DeleteQuery implements CrudQuery
{
    use Where;

    /**
     * constructor.
     * @param string       $table      対象テーブル
     * @param Expression[] $wheres     Where条件
     */
    public function __construct(
        protected string $table = '',
        array $wheres = [],
    ) {
        $this->wheres = $wheres;
    }

    /**
     * 対象テーブルの設定
     * @param string $table
     * @return $this
     */
    public function from(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function toQuery(): string
    {
        $query = sprintf(
            'DELETE FROM %s',
            $this->table,
        );

        // Where
        if (count($this->wheres) > 0)
        {
            $query .= ' ' . $this->toWhereQuery();
        }

        return $query;
    }

    /**
     * {@inheritDoc}
     */
    public function toParameters(): array
    {
        return $this->toWhereParameters();
    }

    /**
     * {@inheritDoc}
     */
    public function toQueryPack(): QueryPack
    {
        return new QueryPack($this->toQuery(), $this->toParameters());
    }
}
