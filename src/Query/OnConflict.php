<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2026, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query;

/**
 * On Conflict
 */
trait OnConflict
{
    /** @var string[] */
    protected array $conflicts = [];

    /** @var bool DO UPDATE にするか(false なら DO NOTHING) */
    protected bool $conflictUpdate = false;

    /** @var array<string, string|int|bool|float|RawExpr|null> DO UPDATE の SET(カラム => 値か RawExpr) */
    protected array $conflictSets = [];

    /**
     * On Conflict Do Nothing
     * @param string[] $columns 一意性を見るカラム
     * @return $this
     */
    public function onConflictDoNothing(array $columns): self
    {
        $this->conflicts = $columns;
        $this->conflictUpdate = false;
        $this->conflictSets = [];
        return $this;
    }

    /**
     * On Conflict Do Update
     * @param string[]                                          $columns 一意性を見るカラム
     * @param array<string, string|int|bool|float|RawExpr|null> $sets    カラム => 値か RawExpr
     * @return $this
     */
    public function onConflictDoUpdate(array $columns, array $sets): self
    {
        $this->conflicts = $columns;
        $this->conflictUpdate = true;
        $this->conflictSets = $sets;
        return $this;
    }

    /**
     * クエリ生成
     * @return string
     */
    public function toOnConflictQuery(): string
    {
        $query = sprintf('ON CONFLICT (%s)', implode(', ', $this->conflicts));
        if ($this->conflictUpdate === false)
        {
            return $query . ' DO NOTHING';
        }

        return $query . ' DO UPDATE SET ' . (new Assignments($this->conflictSets))->toQuery();
    }

    /**
     * パラメーター生成(DO UPDATE の SET の値。DO NOTHING のときと、ON CONFLICT の句を書かないとき(一意性を見るカラムが無い)は空)
     * @return array<int, mixed>
     */
    public function toOnConflictParameters(): array
    {
        if (count($this->conflicts) === 0)
        {
            return [];
        }

        return (new Assignments($this->conflictSets))->toParameters();
    }
}
