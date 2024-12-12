<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query\Set;

use Citrus\Query\Where\Expression;

/**
 * プロパティのペア
 */
class PropertyPair implements Expression
{
    /**
     * constructor.
     * @param string                             $key
     * @param string|int|bool|float|RawExpr|null $value
     */
    public function __construct(
        protected string                             $key,
        protected string|int|bool|float|RawExpr|null $value,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function toQuery(): string
    {
        // 生クエリの場合はプリペアせずに設定する
        if ($this->value instanceof RawExpr) {
            return sprintf(
                '%s = %s',
                $this->key,
                $this->value->toQuery()
            );
        }

        return sprintf(
            '%s = ?',
            $this->key,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function operator(): string
    {
        return '=';
    }

    /**
     * {@inheritDoc}
     */
    public function parameters(): array
    {
        return [$this->value];
    }
}
