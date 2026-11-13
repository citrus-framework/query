<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2026, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test\Query;

use Citrus\Database\DatabaseException;
use Citrus\Query\BulkInsertQuery;
use PHPUnit\Framework\TestCase;

/**
 * Bulk Insert Query Test
 */
class BulkInsertQueryTest extends TestCase
{
    /**
     * @test
     */
    public function testToQueryAndToParameters()
    {
        $bulkInsertQuery = new BulkInsertQuery('samples');
        $bulkInsertQuery->properties([
            ['sample_id' => 'a', 'name' => 'b'],
            ['sample_id' => 'c', 'name' => 'd'],
            ['sample_id' => 'e', 'name' => null],
        ]);

        $this->assertSame('INSERT INTO samples (sample_id, name) VALUES (?, ?), (?, ?), (?, ?)', $bulkInsertQuery->toQuery());
        $this->assertSame(['a', 'b', 'c', 'd', 'e', null], $bulkInsertQuery->toParameters());
    }

    /**
     * @test
     */
    public function testPropertiesWithDifferentColumns()
    {
        $this->expectException(DatabaseException::class);

        (new BulkInsertQuery('samples'))->properties([
            ['sample_id' => 'a', 'name' => 'b'],
            ['name' => 'd', 'sample_id' => 'c'],
        ]);
    }

    /**
     * @test
     */
    public function testOnConflictDoNothingAndReturning()
    {
        $bulkInsertQuery = new BulkInsertQuery('samples');
        $bulkInsertQuery
            ->properties([
                ['code' => 'a', 'name' => 'b'],
                ['code' => 'c', 'name' => 'd'],
            ])
            ->onConflictDoNothing(['code'])
            ->returning(['sample_id']);

        $this->assertSame('INSERT INTO samples (code, name) VALUES (?, ?), (?, ?) ON CONFLICT (code) DO NOTHING RETURNING sample_id', $bulkInsertQuery->toQuery());
        $this->assertSame(['a', 'b', 'c', 'd'], $bulkInsertQuery->toParameters());
    }
}
