<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test\Query;

use Citrus\Query\UpdateQuery;
use PHPUnit\Framework\TestCase;

/**
 * Update Query Test
 */
class UpdateQueryTest extends TestCase
{
    /**
     * @test
     */
    public function testToQueryAndToParameters()
    {
        $updateQuery = new UpdateQuery('samples');
        $updateQuery->properties([
            'sample_id' => 'a',
            'name' => 'b',
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

        $this->assertSame('UPDATE samples SET sample_id = ?, name = ? WHERE sample_id = ? AND sample_id > ? AND sample_id >= ? AND sample_id IN (?, ?) AND sample_id < ? AND sample_id <= ? AND sample_id != ? AND sample_id NOT IN (?, ?, ?)', $updateQuery->toQuery());
        $this->assertSame(['a', 'b', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11'], $updateQuery->toParameters());
    }
}
