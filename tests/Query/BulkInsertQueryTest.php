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
use Citrus\Query\RawExpr;
use PHPUnit\Framework\TestCase;

/**
 * Bulk Insert Query Test
 */
class BulkInsertQueryTest extends TestCase
{
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

    public function testRawExprInValues()
    {
        $bulkInsertQuery = new BulkInsertQuery('samples');
        $bulkInsertQuery->properties([
            ['a' => 1, 'b' => new RawExpr('CAST(? AS jsonb)', ['{}'])],
            ['a' => 2, 'b' => new RawExpr('CAST(? AS jsonb)', ['[]'])],
            ['a' => 3, 'b' => new RawExpr('current_timestamp')],
        ]);

        $this->assertSame('INSERT INTO samples (a, b) VALUES (?, CAST(? AS jsonb)), (?, CAST(? AS jsonb)), (?, current_timestamp)', $bulkInsertQuery->toQuery());
        $this->assertSame([1, '{}', 2, '[]', 3], $bulkInsertQuery->toParameters());
    }

    public function testOnConflictDoUpdate()
    {
        $bulkInsertQuery = new BulkInsertQuery('samples');
        $bulkInsertQuery
            ->properties([
                ['code' => 'a', 'count' => 1],
                ['code' => 'b', 'count' => 2],
            ])
            ->onConflictDoUpdate(
                ['code'],
                [
                    'count' => new RawExpr('samples.count + EXCLUDED.count'),
                    'name' => 'n',
                    'meta' => new RawExpr('CAST(? AS jsonb)', ['{}']),
                ],
            )
            ->returning(['sample_id']);

        $this->assertSame('INSERT INTO samples (code, count) VALUES (?, ?), (?, ?) ON CONFLICT (code) DO UPDATE SET count = samples.count + EXCLUDED.count, name = ?, meta = CAST(? AS jsonb) RETURNING sample_id', $bulkInsertQuery->toQuery());
        $this->assertSame(['a', 1, 'b', 2, 'n', '{}'], $bulkInsertQuery->toParameters());
    }

    public function testOnConflictLaterCallWins()
    {
        $bulkInsertQuery = new BulkInsertQuery('samples');
        $bulkInsertQuery
            ->properties([['code' => 'a', 'name' => 'b']])
            ->onConflictDoUpdate(['code'], ['name' => 'c'])
            ->onConflictDoNothing(['code']);

        $this->assertSame('INSERT INTO samples (code, name) VALUES (?, ?) ON CONFLICT (code) DO NOTHING', $bulkInsertQuery->toQuery());
        $this->assertSame(['a', 'b'], $bulkInsertQuery->toParameters());

        $bulkInsertQuery->onConflictDoUpdate(['code'], ['name' => 'c']);

        $this->assertSame('INSERT INTO samples (code, name) VALUES (?, ?) ON CONFLICT (code) DO UPDATE SET name = ?', $bulkInsertQuery->toQuery());
        $this->assertSame(['a', 'b', 'c'], $bulkInsertQuery->toParameters());
    }

    public function testPropertiesWithDifferentColumns()
    {
        $this->expectException(DatabaseException::class);

        (new BulkInsertQuery('samples'))->properties([
            ['sample_id' => 'a', 'name' => 'b'],
            ['name' => 'd', 'sample_id' => 'c'],
        ]);
    }

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
