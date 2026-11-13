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

    /**
     * On Conflict Do Nothing
     * @param string[] $columns 一意性を見るカラム
     * @return $this
     */
    public function onConflictDoNothing(array $columns): self
    {
        $this->conflicts = $columns;
        return $this;
    }

    /**
     * クエリ生成
     * @return string
     */
    public function toOnConflictQuery(): string
    {
        return sprintf('ON CONFLICT (%s) DO NOTHING', implode(', ', $this->conflicts));
    }
}
