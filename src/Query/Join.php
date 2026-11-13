<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2026, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query;

/**
 * Join
 */
trait Join
{
    /** @var array<int, array{0: string, 1: array<string, string>}> */
    protected array $joins = [];

    /**
     * Inner Join
     * 複数回呼ぶと、呼んだ順に並べる
     * @param string                $table 結合するテーブル(別名を付けるときは 'テーブル 別名')
     * @param array<string, string> $on    結合の条件(カラム => カラム。複数あればANDでつなぐ)
     * @return $this
     */
    public function join(string $table, array $on): self
    {
        $this->joins[] = [$table, $on];
        return $this;
    }

    /**
     * クエリ生成
     * @return string
     */
    public function toJoinQuery(): string
    {
        $queries = [];
        foreach ($this->joins as [$table, $on])
        {
            $conditions = [];
            foreach ($on as $left => $right)
            {
                $conditions[] = sprintf('%s = %s', $left, $right);
            }
            $queries[] = sprintf('INNER JOIN %s ON %s', $table, implode(' AND ', $conditions));
        }
        return implode(' ', $queries);
    }
}
