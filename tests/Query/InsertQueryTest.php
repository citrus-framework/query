<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test\Query;

use Citrus\Query\InsertQuery;
use PHPUnit\Framework\TestCase;

/**
 * Insert Query Test
 */
class InsertQueryTest extends TestCase
{
    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
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
