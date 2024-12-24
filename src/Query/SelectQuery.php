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
 * Select Query Builder
 */
class SelectQuery implements CrudQuery
{
    use Where;
    use LimitOffset;
    use OrderBy;

    /**
     * constructor.
     * @param string       $table       対象テーブル
     * @param array        $properties  取得プロパティ
     * @param string       $resultClass 結果セットクラス
     * @param Expression[] $wheres      Where条件
     */
    public function __construct(
        protected string $table = '',
        protected array $properties = [],
        protected string $resultClass = \stdClass::class,
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
     * 取得プロパティの定義
     * @param string[] $properties
     * @return SelectQuery
     */
    public function properties(array $properties): self
    {
        $this->properties = $properties;
        return $this;
    }

    /**
     * 結果セットクラスの設定
     * @param string $resultClass
     * @return self
     */
    public function resultClass(string $resultClass): self
    {
        $this->resultClass = $resultClass;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function toQuery(): string
    {
        // Columns
        $columns = count($this->properties) > 0
            ? implode(', ', $this->properties)
            : '*';

        $query = sprintf(
            'SELECT %s FROM %s',
            $columns,
            $this->table,
        );

        // Where
        if (count($this->wheres) > 0)
        {
            $query .= ' ' . $this->toWhereQuery();
        }

        // Order By
        if (count($this->orders) > 0)
        {
            $query .= ' ' . $this->toOrderByQuery();
        }

        // Limit / Offset
        $query .= ' ' . $this->toLimitOffsetQuery();

        return $query;
    }

    /**
     * {@inheritDoc}
     */
    public function toParameters(): array
    {
        return array_merge($this->toWhereParameters(), $this->toLimitOffsetParameters());
    }

    /**
     * {@inheritDoc}
     */
    public function toQueryPack(): QueryPack
    {
        return new QueryPack($this->toQuery(), $this->toParameters(), $this->resultClass);
    }
}
