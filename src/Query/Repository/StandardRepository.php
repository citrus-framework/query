<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query\Repository;

use Citrus\Query\ResultSet\Record;
use Citrus\Query\ResultSet\ResultClass;
use Citrus\Variable\Singleton;

/**
 * StandardRepository
 */
abstract class StandardRepository extends Repository
{
    use Singleton;

    /**
     * テーブル名の取得
     * @return string
     */
    abstract public function table(): string;

    /**
     * 結果クラス名の取得
     * @return string
     */
    abstract public function resultClass(): string;

    /**
     * 永続化
     * @param Record $property
     * @return int
     */
    public function persist(Record $property): int
    {
        $property->completeForCreate();
        return $this->executor->build($this
            ->builder($this->table())
            ->insertQuery()
            ->properties($property->properties())
        )->execute();
    }

    /**
     * 更新
     * @param Record  $property
     * @param Record $condition
     * @return int
     */
    public function modify(Record $property, Record $condition): int
    {
        $property->completeForUpdate();
        return $this->executor->build($this
            ->builder($this->table())
            ->updateQuery()
            ->properties($property->nonnullProperties())
            ->whereEqual('rowid', $condition->rowid)
            ->whereEqual('rev', $condition->rev)
        )->execute();
    }

    /**
     * 削除
     * @param Record $condition
     * @return int
     */
    public function remove(Record $condition): int
    {
        return $this->executor->build($this
            ->builder($this->table())
            ->deleteQuery()
            ->whereEqual('rowid', $condition->rowid)
            ->whereEqual('rev', $condition->rev)
        )->execute();
    }

    /**
     * 取得(単数)
     * @param Record $condition
     * @return ResultClass|null
     */
    public function find(Record $condition): ResultClass|null
    {
        $builder = $this
            ->builder($this->table())
            ->selectQuery()
            ->resultClass($this->resultClass());
        foreach ($condition->primaryKeys() as $column)
        {
            $builder->whereEqual($column, $condition->{$column});
        }
        return $this->executor->build($builder)->fetch()->one();
    }
}
