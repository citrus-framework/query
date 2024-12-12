<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query;

use Citrus\Database\QueryPack;
use Citrus\Query\Set\PropertyPair;
use Citrus\Query\Set\RawExpr;
use Citrus\Query\Where\Expression;

/**
 * Update Query Builder
 */
class UpdateQuery implements CrudQuery
{
    use Where;

    /**
     * constructor.
     * @param string         $table      対象テーブル
     * @param PropertyPair[] $properties 取得プロパティ
     * @param Expression[]   $wheres     Where条件
     */
    public function __construct(
        protected string $table = '',
        protected array $properties = [],
        array $wheres = [],
    ) {
        $this->wheres = $wheres;
    }

    /**
     * 対象テーブルの設定
     * @param string $table
     * @return $this
     */
    public function table(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    /**
     * 保存プロパティの定義
     * @param PropertyPair[] $properties
     * @return $this
     */
    public function properties(array $properties): self
    {
        $this->properties = $properties;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function toQuery(): string
    {
        $query = sprintf(
            'UPDATE %s',
            $this->table,
        );

        // Set
        $sets = [];
        foreach ($this->properties as $propertyPair)
        {
            $sets[] = $propertyPair->toQuery();
        }
        $query .= ' SET ' . implode(', ', $sets);

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
        $properties = [];
        foreach ($this->properties as $propertyPair)
        {
            $properties = array_merge($properties, $propertyPair->parameters());
        }
        return array_merge($properties, $this->toWhereParameters());
    }

    /**
     * {@inheritDoc}
     */
    public function toQueryPack(): QueryPack
    {
        return new QueryPack($this->toQuery(), $this->toParameters());
    }
}
