<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query;

/**
 * 生のクエリパーツ文字列
 */
class RawExpr
{
    /**
     * constructor.
     * @param string                                  $raw        式
     * @param array<int, string|int|bool|float|null> $parameters 式の中の `?` に入れる値
     */
    public function __construct(
        protected string $raw,
        protected array $parameters = [],
    ) {
    }

    /**
     * 式の中の `?` に入れる値
     * @return array<int, string|int|bool|float|null>
     */
    public function parameters(): array
    {
        return $this->parameters;
    }

    /**
     * クエリとして出力
     */
    public function toQuery(): string
    {
        return $this->raw;
    }
}
