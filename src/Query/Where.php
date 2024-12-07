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
 * Where Query Part Builder
 */
trait Where
{
    /** @var Expression[] */
    protected array $wheres = [];

    /**
     * Where =
     * @param string $arg1
     * @param string $arg2
     * @return $this
     */
    public function whereEqual(string $arg1, string $arg2): self
    {
        $this->wheres[] = new Equal($arg1, $arg2);
        return $this;
    }

    /**
     * Where !=
     * @param string $arg1
     * @param string $arg2
     * @return $this
     */
    public function whereNotEqual(string $arg1, string $arg2): self
    {
        $this->wheres[] = new NotEqual($arg1, $arg2);
        return $this;
    }

    /**
     * Where <
     * @param string $arg1
     * @param string $arg2
     * @return $this
     */
    public function whereLessThan(string $arg1, string $arg2): self
    {
        $this->wheres[] = new LessThan($arg1, $arg2);
        return $this;
    }

    /**
     * Where <=
     * @param string $arg1
     * @param string $arg2
     * @return $this
     */
    public function whereLessThanEqual(string $arg1, string $arg2): self
    {
        $this->wheres[] = new LessThanEqual($arg1, $arg2);
        return $this;
    }

    /**
     * Where >
     * @param string $arg1
     * @param string $arg2
     * @return $this
     */
    public function whereGreaterThan(string $arg1, string $arg2): self
    {
        $this->wheres[] = new GreaterThan($arg1, $arg2);
        return $this;
    }

    /**
     * Where <=
     * @param string $arg1
     * @param string $arg2
     * @return $this
     */
    public function whereGreaterThanEqual(string $arg1, string $arg2): self
    {
        $this->wheres[] = new GreaterThanEqual($arg1, $arg2);
        return $this;
    }

    /**
     * Where In
     * @param string   $arg1
     * @param string[] $arg2 // TODO: サブクエリも対応したい
     * @return $this
     */
    public function whereIn(string $arg1, array $arg2): self
    {
        $this->wheres[] = new In($arg1, $arg2);
        return $this;
    }

    /**
     * Where Not In
     * @param string   $arg1
     * @param string[] $arg2 // TODO: サブクエリも対応したい
     * @return $this
     */
    public function whereNotIn(string $arg1, array $arg2): self
    {
        $this->wheres[] = new NotIn($arg1, $arg2);
        return $this;
    }

    /**
     * クエリ生成
     * @return string
     */
    public function toWhereQuery(): string
    {
        $query = '';
        if (count($this->wheres) > 0)
        {
            $query .= 'WHERE ' . implode(' AND ', array_map(function (Expression $expr) {
                return $expr->toQuery();
            }, $this->wheres));
        }
        return $query;
    }

    /**
     * パラメーター生成
     * @return string[]
     */
    public function toWhereParameters(): array
    {
        $parameters = [];
        foreach ($this->wheres as $where)
        {
            $parameters = array_merge($parameters, $where->parameters());
        }
        return $parameters;
    }
}
