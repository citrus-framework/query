<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2026, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query\Where;

use Citrus\Query\Bindings;
use Citrus\Query\RawExpr;

/**
 * Where Part In Array
 * 配列のカラムが、値を含むか(<値> = ANY(<column>))
 */
class InArray implements Expression
{
    /**
     * constructor.
     * @param string             $column 配列のカラム
     * @param string|int|RawExpr $value  含むかを見る値
     */
    public function __construct(
        protected string $column,
        protected string|int|RawExpr $value,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function query(): string
    {
        return sprintf(
            '%s %s(%s)',
            Bindings::placeholder($this->value),
            $this->operator(),
            $this->column,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function operator(): string
    {
        return '= ANY';
    }

    /**
     * {@inheritDoc}
     */
    public function parameters(): array
    {
        return Bindings::parameters([$this->value]);
    }
}
