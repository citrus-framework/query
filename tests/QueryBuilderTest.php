<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test;

use Citrus\Query\Builder;
use Citrus\Query\RawExpr;
use PHPUnit\Framework\TestCase;

/**
 * Query Builder Test
 */
class QueryBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function testDeleteQuery()
    {
        $deleteQuery = (new Builder('samples'))->deleteQuery();
        $deleteQuery->whereEqual('sample_id', '1')
            ->whereGreaterThan('sample_id', '2')
            ->whereGreaterThanEqual('sample_id', '3')
            ->whereIn('sample_id', ['4', '5'])
            ->whereLessThan('sample_id', '6')
            ->whereLessThanEqual('sample_id', '7')
            ->whereNotEqual('sample_id', '8')
            ->whereNotIn('sample_id', ['9', '10', '11']);

        $this->assertSame('DELETE FROM samples WHERE sample_id = ? AND sample_id > ? AND sample_id >= ? AND sample_id IN (?, ?) AND sample_id < ? AND sample_id <= ? AND sample_id != ? AND sample_id NOT IN (?, ?, ?)', $deleteQuery->toQuery());
        $this->assertSame(['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11'], $deleteQuery->toParameters());
    }

    /**
     * @test
     */
    public function testInsertQuery()
    {
        $insertQuery = (new Builder('samples'))->insertQuery();
        $insertQuery->properties([
            'sample_id' => 'a',
            'name' => 'b',
        ]);

        $this->assertSame('INSERT INTO samples (sample_id, name) VALUES (?, ?)', $insertQuery->toQuery());
        $this->assertSame(['a', 'b'], $insertQuery->toParameters());
    }

    /**
     * @test
     */
    public function testSelectQuery()
    {
        $selectQuery = (new Builder('samples'))->selectQuery()->properties(['sample_id', 'name', 'created_at']);
        $selectQuery
            ->whereEqual('sample_id', '1')
            ->whereGreaterThan('sample_id', '2')
            ->whereGreaterThanEqual('sample_id', '3')
            ->whereIn('sample_id', ['4', '5'])
            ->whereLessThan('sample_id', '6')
            ->whereLessThanEqual('sample_id', '7')
            ->whereNotEqual('sample_id', '8')
            ->whereNotIn('sample_id', ['9', '10', '11']);

        $this->assertSame('SELECT sample_id, name, created_at FROM samples WHERE sample_id = ? AND sample_id > ? AND sample_id >= ? AND sample_id IN (?, ?) AND sample_id < ? AND sample_id <= ? AND sample_id != ? AND sample_id NOT IN (?, ?, ?) ', $selectQuery->toQuery());
        $this->assertSame(['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11'], $selectQuery->toParameters());
    }

    /**
     * @test
     */
    public function testUpdateQuery()
    {
        $updateQuery = (new Builder('samples'))->updateQuery()->properties([
            'sample_id' => 'a',
            'name' => new RawExpr('b + 1'),
        ]);
        $updateQuery
            ->whereEqual('sample_id', '1')
            ->whereGreaterThan('sample_id', '2')
            ->whereGreaterThanEqual('sample_id', '3')
            ->whereIn('sample_id', ['4', '5'])
            ->whereLessThan('sample_id', '6')
            ->whereLessThanEqual('sample_id', '7')
            ->whereNotEqual('sample_id', '8')
            ->whereNotIn('sample_id', ['9', '10', '11']);

        $this->assertSame('UPDATE samples SET sample_id = ?, name = b + 1 WHERE sample_id = ? AND sample_id > ? AND sample_id >= ? AND sample_id IN (?, ?) AND sample_id < ? AND sample_id <= ? AND sample_id != ? AND sample_id NOT IN (?, ?, ?)', $updateQuery->toQuery());
        $this->assertSame(['a', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11'], $updateQuery->toParameters());
    }
}
