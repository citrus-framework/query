<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2026, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query;

/**
 * 値の並びから、プレースホルダーとパラメーターを作る
 * 値が RawExpr のときは、`?` の代わりに式を書き、パラメーターは式の値にする
 */
class Bindings
{
    /**
     * 値1つぶんのプレースホルダー
     * @param mixed $value
     * @return string
     */
    public static function placeholder(mixed $value): string
    {
        return $value instanceof RawExpr ? $value->toQuery() : '?';
    }

    /**
     * 値の並びぶんのプレースホルダーを、カンマ区切りで返す
     * @param array<array-key, mixed> $values
     * @return string
     */
    public static function placeholders(array $values): string
    {
        return implode(', ', array_map(self::placeholder(...), $values));
    }

    /**
     * 値の並びぶんのパラメーターを、並び順で返す
     * @param array<array-key, mixed> $values
     * @return array<int, mixed>
     */
    public static function parameters(array $values): array
    {
        $parameters = [];
        foreach ($values as $value)
        {
            if ($value instanceof RawExpr)
            {
                // 式の値を、その場所の順で入れる
                $parameters = array_merge($parameters, $value->parameters());
                continue;
            }

            $parameters[] = $value;
        }
        return $parameters;
    }
}
