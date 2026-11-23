<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query;

/**
 * Limit / Offset
 */
trait LimitOffset
{
    /** @var int|null */
    protected int|null $limit = null;

    /** @var int|null */
    protected int|null $offset = null;

    /**
     * Limit
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Offset
     * @param int $offset
     * @return $this
     */
    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * pagination
     * @param int|null $page
     * @param int|null $limit
     * @return $this
     */
    public function pagination(int|null $page, int|null $limit): self
    {
        if (is_null($page) or is_null($limit))
        {
            return $this;
        }

        $this->limit = $limit;
        $this->offset = ($page * $limit) - $limit;
        return $this;
    }

    /**
     * クエリ生成
     * @return string
     */
    public function toLimitOffsetQuery(): string
    {
        return (new LimitOffsetClause($this->limit, $this->offset))->toQuery();
    }

    /**
     * パラメーター生成
     * @return string[]
     */
    public function toLimitOffsetParameters(): array
    {
        return (new LimitOffsetClause($this->limit, $this->offset))->toParameters();
    }
}
