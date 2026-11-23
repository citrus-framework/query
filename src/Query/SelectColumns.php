<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2026, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query;

use Citrus\Query\ResultSet\ResultCount;

/**
 * SELECT で取得するカラムの句
 */
class SelectColumns
{
    /**
     * constructor.
     * @param array  $properties  取得プロパティ
     * @param string $resultClass 結果セットクラス
     */
    public function __construct(
        protected array $properties,
        protected string $resultClass,
    ) {
    }

    /**
     * クエリ生成
     * @return string
     */
    public function toQuery(): string
    {
        // 結果クラスがカウントの場合はプロパティをcount(*) as countにする
        if (count($this->properties) === 0 and $this->resultClass === ResultCount::class)
        {
            return 'count(*) AS count';
        }
        if (count($this->properties) === 0)
        {
            return '*';
        }

        return implode(', ', $this->properties);
    }
}
