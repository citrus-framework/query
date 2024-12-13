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
     * @param string $raw
     */
    public function __construct(
        protected string $raw,
    ) {
    }

    /**
     * クエリとして出力
     */
    public function toQuery(): string
    {
        return $this->raw;
    }
}
