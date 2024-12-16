<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test\Query;

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
            ->pagination(3, 10)
        ;

        $this->assertSame('SELECT sample_id, name, created_at FROM samples WHERE sample_id = ? AND sample_id > ? AND sample_id >= ? AND sample_id IN (?, ?) AND sample_id < ? AND sample_id <= ? AND sample_id != ? AND sample_id NOT IN (?, ?, ?) LIMIT ? OFFSET ?', $selectQuery->toQuery());
        $this->assertSame(['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', 10, 20], $selectQuery->toParameters());
    }
}
