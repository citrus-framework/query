<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2026, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query;

/**
 * Limit / Offset の句
 */
class LimitOffsetClause
{
    /**
     * constructor.
     * @param int|null $limit
     * @param int|null $offset
     */
    public function __construct(
        protected int|null $limit,
        protected int|null $offset,
    ) {
    }

    /**
     * クエリ生成
     * @return string
     */
    public function toQuery(): string
    {
        $queries = [];
        if ($this->limit !== null)
        {
            $queries[] = 'LIMIT ?';
        }
        if ($this->offset !== null)
        {
            $queries[] = 'OFFSET ?';
        }
        return implode(' ', $queries);
    }

    /**
     * パラメーター生成
     * @return int[]
     */
    public function toParameters(): array
    {
        return array_values(array_filter(
            [$this->limit, $this->offset],
            static fn (int|null $value): bool => $value !== null,
        ));
    }
}
