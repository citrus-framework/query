<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test\Query;

use Citrus\Database\Connection\Connection;
use Citrus\Query\Builder;
use Citrus\Query\Executor;
use PDO;
use PHPUnit\Framework\TestCase;
use Stringable;

/**
 * Executor Test
 */
class ExecutorTest extends TestCase
{
    /**
     * bindValueの3つめの引数(PDOの型)を確かめる。
     * 記録用のPDOStatementに差し替えて、渡されたPDOの型だけを見る。
     * @test
     */
    public function testBindValueReceivesPdoTypes(): void
    {
        RecordingStatement::$calls = [];
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_STATEMENT_CLASS, [RecordingStatement::class]);
        $pdo->exec('CREATE TABLE samples (a, b, c, d, e, f, g)');

        $stringable = new class implements Stringable {
            public function __toString(): string
            {
                return 'str';
            }
        };
        $insert = (new Builder('samples'))->insertQuery();
        $insert->properties([
            'a' => false,
            'b' => 5,
            'c' => null,
            'd' => 'abc',
            'e' => 1.5,
            'f' => true,
            'g' => $stringable,
        ]);
        (new Executor(new Connection(null, $pdo)))->build($insert)->execute();

        $types = array_column(RecordingStatement::$calls, 2, 0);
        $this->assertSame(PDO::PARAM_BOOL, $types[1]);
        $this->assertSame(PDO::PARAM_INT, $types[2]);
        $this->assertSame(PDO::PARAM_NULL, $types[3]);
        $this->assertSame(PDO::PARAM_STR, $types[4]);
        $this->assertSame(PDO::PARAM_STR, $types[5]);
        $this->assertSame(PDO::PARAM_BOOL, $types[6]);
        $this->assertSame(PDO::PARAM_STR, $types[7]);
    }

    /**
     * sqliteで実際に書き込めることだけを確かめる(動作の確認)。
     * PostgreSQLへの送られ方の根拠にはならない(pdo_pgsqlはboolを't'/'f'で送る)。
     * @test
     */
    public function testExecuteOnSqlite(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE samples (flag_off, flag_on, num, empty_value, text)');

        $insert = (new Builder('samples'))->insertQuery();
        $insert->properties([
            'flag_off' => false,
            'flag_on' => true,
            'num' => 5,
            'empty_value' => null,
            'text' => 'abc',
        ]);
        $count = (new Executor(new Connection(null, $pdo)))->build($insert)->execute();

        $this->assertSame(1, $count);
        $row = $pdo->query('SELECT flag_off, flag_on, num, empty_value, text FROM samples')
            ->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals(0, $row['flag_off']);
        $this->assertEquals(1, $row['flag_on']);
        $this->assertEquals(5, $row['num']);
        $this->assertNull($row['empty_value']);
        $this->assertSame('abc', $row['text']);
    }
}
