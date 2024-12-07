<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test\Query\Where;

use Citrus\Query\Where\Equal;
use Citrus\Query\Where\GreaterThan;
use Citrus\Query\Where\GreaterThanEqual;
use Citrus\Query\Where\In;
use Citrus\Query\Where\LessThan;
use Citrus\Query\Where\LessThanEqual;
use Citrus\Query\Where\NotEqual;
use Citrus\Query\Where\NotIn;
use PHPUnit\Framework\TestCase;

/**
 * Where Part
 */
class ExpressionTest extends TestCase
{
    /**
     * @test
     */
    public function testEqual()
    {
        $equal = new Equal('aaa', '10');

        $this->assertSame('aaa = ?', $equal->toQuery());
        $this->assertSame(['10'], $equal->parameters());
    }

    /**
     * @test
     */
    public function testNotEqual()
    {
        $equal = new NotEqual('aaa', '10');

        $this->assertSame('aaa != ?', $equal->toQuery());
        $this->assertSame(['10'], $equal->parameters());
    }

    /**
     * @test
     */
    public function testLessThan()
    {
        $equal = new LessThan('aaa', '10');

        $this->assertSame('aaa < ?', $equal->toQuery());
        $this->assertSame(['10'], $equal->parameters());
    }

    /**
     * @test
     */
    public function testLessThanEqual()
    {
        $equal = new LessThanEqual('aaa', '10');

        $this->assertSame('aaa <= ?', $equal->toQuery());
        $this->assertSame(['10'], $equal->parameters());
    }

    /**
     * @test
     */
    public function testGreaterThan()
    {
        $equal = new GreaterThan('aaa', '10');

        $this->assertSame('aaa > ?', $equal->toQuery());
        $this->assertSame(['10'], $equal->parameters());
    }

    /**
     * @test
     */
    public function testGreaterThanEqual()
    {
        $equal = new GreaterThanEqual('aaa', '10');

        $this->assertSame('aaa >= ?', $equal->toQuery());
        $this->assertSame(['10'], $equal->parameters());
    }

    /**
     * @test
     */
    public function testIn()
    {
        $equal = new In('aaa', ['10', '11', '12']);

        $this->assertSame('aaa IN (?, ?, ?)', $equal->toQuery());
        $this->assertSame(['10', '11', '12'], $equal->parameters());
    }

    /**
     * @test
     */
    public function testNotIn()
    {
        $equal = new NotIn('aaa', ['10', '11', '12']);

        $this->assertSame('aaa NOT IN (?, ?, ?)', $equal->toQuery());
        $this->assertSame(['10', '11', '12'], $equal->parameters());
    }
}
