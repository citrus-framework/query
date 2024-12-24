<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query\Where;

/**
 * Expression Interface
 */
interface Expression
{
    /**
     * クエリ文字列
     * @return string
     */
    public function query(): string;

    /**
     * オペレーター文字列
     * @return string
     */
    public function operator(): string;

    /**
     * パラメーター配列
     * @return string[]
     */
    public function parameters(): array;
}
