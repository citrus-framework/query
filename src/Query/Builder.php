<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query;

/**
 * クエリビルダー
 */
class Builder
{
    /** @var CrudQuery  */
    protected CrudQuery $query;

    /**
     * constructor.
     * @param string $table 対象テーブル
     */
    public function __construct(
        protected string $table,
    ) {
    }

    /**
     * DELETE
     * @return DeleteQuery
     */
    public function deleteQuery(): DeleteQuery
    {
        $this->query = new DeleteQuery($this->table);
        return $this->query;
    }

    /**
     * INSERT
     * @return InsertQuery
     */
    public function insertQuery(): InsertQuery
    {
        $this->query = new InsertQuery($this->table);
        return $this->query;
    }

    /**
     * SELECT
     * @return SelectQuery
     */
    public function selectQuery(): SelectQuery
    {
        $this->query = new SelectQuery($this->table);
        return $this->query;
    }

    /**
     * UPDATE
     * @return UpdateQuery
     */
    public function updateQuery(): UpdateQuery
    {
        $this->query = new UpdateQuery($this->table);
        return $this->query;
    }
}
