<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test\Query;

use Citrus\Query\ResultSet\ResultCount;
use Citrus\Query\SelectQuery;
use PHPUnit\Framework\TestCase;

/**
 * Select Query Test
 */
class SelectQueryTest extends TestCase
{
    /**
     * @test
     */
    public function testToQueryAndToParameters()
    {
        $selectQuery = new SelectQuery('samples', ['sample_id', 'name', 'created_at']);
        $selectQuery
            ->whereEqual('sample_id', '1')
            ->whereGreaterThan('sample_id', '2')
            ->whereGreaterThanEqual('sample_id', '3')
            ->whereIn('sample_id', ['4', '5'])
            ->whereLessThan('sample_id', '6')
            ->whereLessThanEqual('sample_id', '7')
            ->whereNotEqual('sample_id', '8')
            ->whereNotIn('sample_id', ['9', '10', '11'])
            ->whereLike('sample_id', '%12')
            ->whereNotLike('sample_id', '13_')
            ->whereILike('sample_id', '%14')
            ->whereNotILike('sample_id', '15_')
            ->pagination(3, 10)
            ->orderBy('sample_id')
            ->orderBy('name', false)
        ;

        $this->assertSame('SELECT sample_id, name, created_at FROM samples WHERE sample_id = ? AND sample_id > ? AND sample_id >= ? AND sample_id IN (?, ?) AND sample_id < ? AND sample_id <= ? AND sample_id != ? AND sample_id NOT IN (?, ?, ?) AND sample_id LIKE ? AND sample_id NOT LIKE ? AND sample_id ILIKE ? AND sample_id NOT ILIKE ? ORDER BY sample_id ASC, name DESC LIMIT ? OFFSET ?', $selectQuery->toQuery());
        $this->assertSame(['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '%12', '13_', '%14', '15_', 10, 20], $selectQuery->toParameters());
    }
    /**
     * @test
     */
    public function testCount()
    {
        $selectQuery = new SelectQuery('samples');
        $selectQuery
            ->whereEqual('sample_id', '1')
            ->whereGreaterThan('sample_id', '2')
            ->whereGreaterThanEqual('sample_id', '3')
            ->whereIn('sample_id', ['4', '5'])
            ->whereLessThan('sample_id', '6')
            ->whereLessThanEqual('sample_id', '7')
            ->whereNotEqual('sample_id', '8')
            ->whereNotIn('sample_id', ['9', '10', '11'])
            ->whereLike('sample_id', '%12')
            ->whereNotLike('sample_id', '13_')
            ->pagination(3, 10)
            ->orderBy('sample_id')
            ->orderBy('name', false)
            ->resultClass(ResultCount::class)
        ;

        $this->assertSame('SELECT count(*) AS count FROM samples WHERE sample_id = ? AND sample_id > ? AND sample_id >= ? AND sample_id IN (?, ?) AND sample_id < ? AND sample_id <= ? AND sample_id != ? AND sample_id NOT IN (?, ?, ?) AND sample_id LIKE ? AND sample_id NOT LIKE ? ORDER BY sample_id ASC, name DESC LIMIT ? OFFSET ?', $selectQuery->toQuery());
        $this->assertSame(['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '%12', '13_', 10, 20], $selectQuery->toParameters());
    }
}
