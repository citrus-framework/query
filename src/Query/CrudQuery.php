<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query;

use Citrus\Database\QueryPack;

/**
 * Query Interface
 */
interface CrudQuery
{
    /**
     * クエリ生成
     * @return string
     */
    public function toQuery(): string;

    /**
     * パラメーター生成
     * @return string[]
     */
    public function toParameters(): array;

    /**
     * クエリパックで取得
     * @return QueryPack
     */
    public function toQueryPack(): QueryPack;
}
