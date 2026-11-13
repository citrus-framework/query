<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2026, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query;

/**
 * Group By
 */
trait GroupBy
{
    /** @var string[] */
    protected array $groups = [];

    /**
     * Group By
     * 複数回呼ぶと、呼んだ順に並べる
     * @param string $column
     * @return $this
     */
    public function groupBy(string $column): self
    {
        $this->groups[] = $column;
        return $this;
    }

    /**
     * クエリ生成
     * @return string
     */
    public function toGroupByQuery(): string
    {
        return 'GROUP BY ' . implode(', ', $this->groups);
    }
}
