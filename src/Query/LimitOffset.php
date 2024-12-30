<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query;

use Citrus\Query\Where\Equal;
use Citrus\Query\Where\Expression;
use Citrus\Query\Where\GreaterThan;
use Citrus\Query\Where\GreaterThanEqual;
use Citrus\Query\Where\In;
use Citrus\Query\Where\LessThan;
use Citrus\Query\Where\LessThanEqual;
use Citrus\Query\Where\NotEqual;
use Citrus\Query\Where\NotIn;

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
        $queries = [];
        if (!is_null($this->limit))
        {
            $queries[] = 'LIMIT ?';
        }
        if (!is_null($this->offset))
        {
            $queries[] = 'OFFSET ?';
        }
        return implode(' ', $queries);
    }

    /**
     * パラメーター生成
     * @return string[]
     */
    public function toLimitOffsetParameters(): array
    {
        $parameters = [];
        if (!is_null($this->limit))
        {
            $parameters[] = $this->limit;
        }
        if (!is_null($this->offset))
        {
            $parameters[] = $this->offset;
        }
        return $parameters;
    }
}
