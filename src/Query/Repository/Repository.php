<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query\Repository;

use Citrus\Query\Builder;
use Citrus\Query\Executor;
use Citrus\Query\ResultSet\Record;
use Citrus\Query\ResultSet\ResultClass;

/**
 * Repository
 */
abstract class Repository
{
    /**
     * constructor.
     * @param Executor|null $executor
     */
    public function __construct(
        protected Executor|null $executor = null,
    ) {
        $this->executor = $executor ?? new Executor();
    }

    /**
     * Builderの生成
     * @param string $table
     * @return Builder
     */
    public function builder(string $table): Builder
    {
        return new Builder($table);
    }

    /**
     * 永続化
     * @param Record $property
     * @return int 作用数
     */
    abstract public function persist(Record $property): int;

    /**
     * 更新
     * @param Record $property
     * @param Record $condition
     * @return int 作用数
     */
    abstract public function modify(Record $property, Record $condition): int;

    /**
     * 削除
     * @param Record $condition
     * @return int 作用数
     */
    abstract public function remove(Record $condition): int;

    /**
     * 取得(単数)
     * @param Record $condition
     * @return ResultClass|null
     */
    abstract public function find(Record $condition): ResultClass|null;

    /**
     * 取得(複数)
     * @param Record $condition
     * @return ResultClass[]
     */
    abstract public function filter(Record $condition): array;
}
