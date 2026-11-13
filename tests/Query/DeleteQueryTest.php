<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test\Query;

use Citrus\Query\DeleteQuery;
use PHPUnit\Framework\TestCase;

/**
 * Delete Query Test
 */
class DeleteQueryTest extends TestCase
{
    /**
     * @test
     */
    public function testToQueryAndToParameters()
    {
        $deleteQuery = new DeleteQuery('samples');
        $deleteQuery
            ->whereEqual('sample_id', '1')
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
    public function testWhereNullAndWhereNotNull()
    {
        $deleteQuery = new DeleteQuery('samples');
        $deleteQuery
            ->whereNull('deleted_at')
            ->whereEqual('sample_id', '1')
            ->whereNotNull('created_at');

        $this->assertSame('DELETE FROM samples WHERE deleted_at IS NULL AND sample_id = ? AND created_at IS NOT NULL', $deleteQuery->toQuery());
        $this->assertSame(['1'], $deleteQuery->toParameters());
    }
}
