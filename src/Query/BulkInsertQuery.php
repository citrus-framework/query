<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2026, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query;

use Citrus\Database\DatabaseException;
use Citrus\Database\QueryPack;

/**
 * Bulk Insert Query Builder
 * 複数行を1回の INSERT でまとめて入れる
 */
class BulkInsertQuery implements CrudQuery
{
    use OnConflict;
    use Returning;

    /** @var array<int, array<string, string|int|bool|float|null>> 保存プロパティの一覧(1行に1つ) */
    protected array $properties = [];

    /**
     * constructor.
     * @param string $table 対象テーブル
     */
    public function __construct(
        protected string $table = '',
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
     * 1行に1つの保存プロパティを並べる。どの行も、1行目と同じカラムを同じ順に並べる
     * @param array<int, array<string, string|int|bool|float|null>> $properties
     * @return $this
     * @throws DatabaseException
     */
    public function properties(array $properties): self
    {
        $properties = array_values($properties);
        $columns = array_keys($properties[0] ?? []);
        foreach ($properties as $row)
        {
            DatabaseException::exceptionIf(
                array_keys($row) !== $columns,
                'どの行も、1行目と同じカラムを同じ順に並べてください',
            );
        }

        $this->properties = $properties;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function toQuery(): string
    {
        $columns = array_keys($this->properties[0] ?? []);
        $query = sprintf(
            'INSERT INTO %s (%s) VALUES %s',
            $this->table,
            implode(', ', $columns),
            implode(', ', array_fill(0, count($this->properties), sprintf(
                '(%s)',
                implode(', ', array_fill(0, count($columns), '?')),
            ))),
        );

        // On Conflict
        if (count($this->conflicts) > 0)
        {
            $query .= ' ' . $this->toOnConflictQuery();
        }

        // Returning
        if (count($this->returnings) > 0)
        {
            $query .= ' ' . $this->toReturningQuery();
        }

        return $query;
    }

    /**
     * {@inheritDoc}
     */
    public function toParameters(): array
    {
        return array_merge([], ...array_map('array_values', $this->properties));
    }

    /**
     * {@inheritDoc}
     */
    public function toQueryPack(): QueryPack
    {
        return new QueryPack($this->toQuery(), $this->toParameters());
    }
}
