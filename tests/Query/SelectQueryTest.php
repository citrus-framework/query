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

    public function testWhereNullAndWhereNotNull()
    {
        $selectQuery = new SelectQuery('samples');
        $selectQuery
            ->whereEqual('sample_id', '1')
            ->whereNull('deleted_at')
            ->whereNotNull('name')
            ->limit(1)
        ;

        $this->assertSame('SELECT * FROM samples WHERE sample_id = ? AND deleted_at IS NULL AND name IS NOT NULL LIMIT ?', $selectQuery->toQuery());
        $this->assertSame(['1', 1], $selectQuery->toParameters());
    }

    public function testGroupBy()
    {
        $selectQuery = new SelectQuery('samples', ['sample_id', 'name', 'count(*) AS count']);
        $selectQuery
            ->whereEqual('status', '0')
            ->groupBy('sample_id')
            ->groupBy('name')
            ->orderBy('sample_id')
            ->limit(10)
        ;

        $this->assertSame('SELECT sample_id, name, count(*) AS count FROM samples WHERE status = ? GROUP BY sample_id, name ORDER BY sample_id ASC LIMIT ?', $selectQuery->toQuery());
        $this->assertSame(['0', 10], $selectQuery->toParameters());
    }

    public function testJoin()
    {
        $selectQuery = new SelectQuery('user_tags ut', ['ut.user_tag_id', 't.name']);
        $selectQuery
            ->join('tags t', ['t.tag_id' => 'ut.tag_id'])
            ->join('x_users x', ['x.x_user_id' => 'ut.x_user_id', 'x.version' => 'ut.version'])
            ->whereEqual('t.account_id', '1')
            ->whereNull('x.deleted_at')
            ->groupBy('ut.user_tag_id')
            ->groupBy('t.name')
            ->orderBy('ut.user_tag_id')
            ->pagination(2, 10)
        ;

        $this->assertSame('SELECT ut.user_tag_id, t.name FROM user_tags ut INNER JOIN tags t ON t.tag_id = ut.tag_id INNER JOIN x_users x ON x.x_user_id = ut.x_user_id AND x.version = ut.version WHERE t.account_id = ? AND x.deleted_at IS NULL GROUP BY ut.user_tag_id, t.name ORDER BY ut.user_tag_id ASC LIMIT ? OFFSET ?', $selectQuery->toQuery());
        $this->assertSame(['1', 10, 10], $selectQuery->toParameters());
    }
}
