<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test\Query;

use Citrus\Query\InsertQuery;
use Citrus\Query\RawExpr;
use PHPUnit\Framework\TestCase;

/**
 * Insert Query Test
 */
class InsertQueryTest extends TestCase
{
    public function testToQueryAndToParameters()
    {
        $insertQuery = new InsertQuery('samples');
        $insertQuery->properties([
            'sample_id' => 'a',
            'name' => 'b',
        ]);

        $this->assertSame('INSERT INTO samples (sample_id, name) VALUES (?, ?)', $insertQuery->toQuery());
        $this->assertSame(['a', 'b'], $insertQuery->toParameters());
    }

    public function testOnConflictDoNothing()
    {
        $insertQuery = new InsertQuery('samples');
        $insertQuery
            ->properties([
                'account_id' => 'a',
                'event_uuid' => 'b',
                'name' => 'c',
            ])
            ->onConflictDoNothing(['account_id', 'event_uuid']);

        $this->assertSame('INSERT INTO samples (account_id, event_uuid, name) VALUES (?, ?, ?) ON CONFLICT (account_id, event_uuid) DO NOTHING', $insertQuery->toQuery());
        $this->assertSame(['a', 'b', 'c'], $insertQuery->toParameters());
    }

    public function testRawExprInValues()
    {
        $insertQuery = new InsertQuery('samples');
        $insertQuery->properties([
            'a' => 1,
            'b' => new RawExpr('CAST(? AS jsonb)', ['{}']),
            'c' => new RawExpr('current_timestamp'),
            'd' => 'x',
        ]);

        $this->assertSame('INSERT INTO samples (a, b, c, d) VALUES (?, CAST(? AS jsonb), current_timestamp, ?)', $insertQuery->toQuery());
        $this->assertSame([1, '{}', 'x'], $insertQuery->toParameters());
    }

    public function testOnConflictDoUpdate()
    {
        $insertQuery = new InsertQuery('usage_logs');
        $insertQuery
            ->properties([
                'account_id' => 'a',
                'request_count' => 2,
            ])
            ->onConflictDoUpdate(
                ['account_id'],
                [
                    'request_count' => new RawExpr('usage_logs.request_count + EXCLUDED.request_count'),
                    'name' => 'n',
                    'meta' => new RawExpr('CAST(? AS jsonb)', ['{}']),
                    'rev' => new RawExpr('usage_logs.rev + 1'),
                ],
            )
            ->returning(['usage_log_id']);

        $this->assertSame('INSERT INTO usage_logs (account_id, request_count) VALUES (?, ?) ON CONFLICT (account_id) DO UPDATE SET request_count = usage_logs.request_count + EXCLUDED.request_count, name = ?, meta = CAST(? AS jsonb), rev = usage_logs.rev + 1 RETURNING usage_log_id', $insertQuery->toQuery());
        $this->assertSame(['a', 2, 'n', '{}'], $insertQuery->toParameters());
    }

    public function testOnConflictLaterCallWins()
    {
        $insertQuery = new InsertQuery('samples');
        $insertQuery
            ->properties(['code' => 'a', 'name' => 'b'])
            ->onConflictDoUpdate(['code'], ['name' => 'c'])
            ->onConflictDoNothing(['code']);

        $this->assertSame('INSERT INTO samples (code, name) VALUES (?, ?) ON CONFLICT (code) DO NOTHING', $insertQuery->toQuery());
        $this->assertSame(['a', 'b'], $insertQuery->toParameters());

        $insertQuery->onConflictDoUpdate(['code'], ['name' => 'c']);

        $this->assertSame('INSERT INTO samples (code, name) VALUES (?, ?) ON CONFLICT (code) DO UPDATE SET name = ?', $insertQuery->toQuery());
        $this->assertSame(['a', 'b', 'c'], $insertQuery->toParameters());
    }

    public function testOnConflictDoUpdateWithoutColumnsWritesNoParameters()
    {
        $insertQuery = new InsertQuery('samples');
        $insertQuery
            ->properties(['code' => 'a'])
            ->onConflictDoUpdate([], ['name' => 'c']);

        // 一意性を見るカラムが無ければ ON CONFLICT の句を書かないので、SET の値も渡さない
        $this->assertSame('INSERT INTO samples (code) VALUES (?)', $insertQuery->toQuery());
        $this->assertSame(['a'], $insertQuery->toParameters());
    }

    public function testReturning()
    {
        $insertQuery = new InsertQuery('samples');
        $insertQuery
            ->properties([
                'name' => 'a',
            ])
            ->returning(['sample_id', 'created_at']);

        $this->assertSame('INSERT INTO samples (name) VALUES (?) RETURNING sample_id, created_at', $insertQuery->toQuery());
        $this->assertSame(['a'], $insertQuery->toParameters());
    }
}
