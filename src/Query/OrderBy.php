<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query;

/**
 * Order By
 */
trait OrderBy
{
    /** @var array<string, boolean> */
    protected array $orders = [];

    /**
     * Order By
     * @param string    $column
     * @param bool|null $ascending
     * @return $this
     */
    public function orderBy(string $column, bool $ascending = true): self
    {
        $this->orders[$column] = $ascending;
        return $this;
    }

    /**
     * クエリ生成
     * @return string
     */
    public function toOrderByQuery(): string
    {
        $queries = [];
        foreach ($this->orders as $column => $ascending)
        {
            $queries[] = sprintf('%s %s', $column, $ascending ? 'ASC' : 'DESC');
        }
        return 'ORDER BY ' . implode(', ', $queries);
    }
}
