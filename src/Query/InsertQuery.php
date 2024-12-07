<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query;

use Citrus\Database\QueryPack;

/**
 * Insert Query Builder
 */
class InsertQuery implements CrudQuery
{
    /**
     * constructor.
     * @param string $table      対象テーブル
     * @param array  $properties 取得プロパティ
     */
    public function __construct(
        protected string $table = '',
        protected array $properties = [],
    ) {
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
     * @param string[] $properties
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
        return sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', array_keys($this->properties)),
            implode(', ', array_fill(0, count($this->properties), '?')),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toParameters(): array
    {
        return array_values($this->properties);
    }

    /**
     * {@inheritDoc}
     */
    public function toQueryPack(): QueryPack
    {
        return new QueryPack($this->toQuery(), $this->toParameters());
    }
}
