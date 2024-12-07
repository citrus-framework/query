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
}
