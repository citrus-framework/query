<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query;

use Citrus\Database\Connection\Connection;
use Citrus\Database\Connection\ConnectionPool;
use Citrus\Database\DatabaseException;
use Citrus\Database\ResultSet\ResultSet;
use PDOStatement;

/**
 * クエリ実行
 */
class Executor
{
    /** @var CrudQuery  */
    protected CrudQuery $query;

    /**
     * constructor.
     * @param Connection|null $connection 接続情報
     * @throws DatabaseException
     */
    public function __construct(
        protected Connection|null $connection = null
    ) {
        // なければPOOLから取得
        $this->connection = $connection ?: ConnectionPool::callDefault();
        // 接続もしてしまう
        $this->connection->connect();
    }

    /**
     * クエリーを受け取ってビルド
     * @param CrudQuery $query
     * @return $this
     */
    public function build(CrudQuery $query): self
    {
        $this->query = $query;
        return $this;
    }

    /**
     * クエリの実行 INSERT/UPDATE/DELETE
     * @return int
     */
    public function execute(): int
    {
        // プリペア
        $statement = $this->prepareAndBind();

        // 実行
        $statement->execute();

        // 作用した件数を返す
        return $statement->rowCount();
    }

    /**
     * クエリの実行 SELECT
     * @return ResultSet
     */
    public function fetch(): ResultSet
    {
        // プリペアとパラメータ設定
        $statement = $this->prepareAndBind();

        return new ResultSet($statement, $this->query->toQueryPack()->callResultClass());
    }

    /**
     * @return PDOStatement
     * @throws DatabaseException
     */
    protected function prepareAndBind(): PDOStatement
    {
        // ハンドル
        $handle = $this->connection->callHandle();

        // クエリパック
        $queryPack = $this->query->toQueryPack();

        // プリペア実行
        $statement = $handle->prepare($queryPack->callQuery());
        if (false === $statement)
        {
            throw DatabaseException::pdoErrorInfo($handle->errorInfo());
        }

        // パラメータ設定
        foreach ($queryPack->callParameters() as $ky => $vl)
        {
            $statement->bindValue($ky + 1, $vl);
        }

        return $statement;
    }
}
