<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2026, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query;

/**
 * SET の代入式(カラム = 値か式)
 * UPDATE の SET と、ON CONFLICT DO UPDATE の SET で使う
 */
class Assignments
{
    /**
     * constructor.
     * @param array<string, string|int|bool|float|RawExpr|null> $values カラム => 値か RawExpr
     */
    public function __construct(
        protected array $values,
    ) {
    }

    /**
     * クエリ生成
     * @return string
     */
    public function toQuery(): string
    {
        $sets = [];
        foreach ($this->values as $column => $value)
        {
            $sets[] = sprintf('%s = %s', $column, Bindings::placeholder($value));
        }
        return implode(', ', $sets);
    }

    /**
     * パラメーター生成
     * @return array<int, mixed>
     */
    public function toParameters(): array
    {
        return Bindings::parameters($this->values);
    }
}
