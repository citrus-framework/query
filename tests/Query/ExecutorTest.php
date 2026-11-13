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

    /**
     * 複数行のINSERTで、各行の値が順に、位置とPDOの型を合わせて渡ることを確かめる。
     * @test
     */
    public function testBulkInsertBindsValuesInOrder(): void
    {
        RecordingStatement::$calls = [];
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_STATEMENT_CLASS, [RecordingStatement::class]);
        $pdo->exec('CREATE TABLE samples (name, num)');

        $insert = (new Builder('samples'))->bulkInsertQuery();
        $insert->properties([
            ['name' => 'a', 'num' => 1],
            ['name' => 'b', 'num' => null],
        ]);
        $count = (new Executor(new Connection(null, $pdo)))->build($insert)->execute();

        $this->assertSame([
            [1, 'a', PDO::PARAM_STR],
            [2, 1, PDO::PARAM_INT],
            [3, 'b', PDO::PARAM_STR],
            [4, null, PDO::PARAM_NULL],
        ], RecordingStatement::$calls);
        $this->assertSame(2, $count);
        $rows = $pdo->query('SELECT name, num FROM samples ORDER BY name')
            ->fetchAll(PDO::FETCH_ASSOC);
        $this->assertSame([
            ['name' => 'a', 'num' => 1],
            ['name' => 'b', 'num' => null],
        ], $rows);
    }

    /**
     * RETURNINGで返った値を、executeReturning()で読めることを確かめる(sqliteでの動作の確認)。
     * @test
     */
    public function testExecuteReturningOnSqlite(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE samples (sample_id INTEGER PRIMARY KEY AUTOINCREMENT, name)');

        $insert = (new Builder('samples'))->insertQuery();
        $insert
            ->properties(['name' => 'a'])
            ->returning(['sample_id', 'name']);
        $rows = (new Executor(new Connection(null, $pdo)))->build($insert)->executeReturning();

        $this->assertSame([['sample_id' => 1, 'name' => 'a']], $rows);

        // 複数行(返る行の順はsqliteでは決まっていないので、sample_idで並べて比べる)
        $insert = (new Builder('samples'))->bulkInsertQuery();
        $insert
            ->properties([
                ['name' => 'b'],
                ['name' => 'c'],
            ])
            ->returning(['sample_id', 'name']);
        $rows = (new Executor(new Connection(null, $pdo)))->build($insert)->executeReturning();
        $names = array_column($rows, 'name', 'sample_id');
        ksort($names);

        $this->assertSame([2 => 'b', 3 => 'c'], $names);
    }

    /**
     * ON CONFLICT DO NOTHINGで、重なった行は入らず、RETURNINGにも返らないことを確かめる(sqliteでの動作の確認)。
     * @test
     */
    public function testOnConflictDoNothingOnSqlite(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE samples (sample_id INTEGER PRIMARY KEY AUTOINCREMENT, code UNIQUE)');
        $pdo->exec("INSERT INTO samples (code) VALUES ('a')");
        $executor = new Executor(new Connection(null, $pdo));

        $insert = (new Builder('samples'))->insertQuery();
        $insert
            ->properties(['code' => 'a'])
            ->onConflictDoNothing(['code']);
        $this->assertSame(0, $executor->build($insert)->execute());

        $insert = (new Builder('samples'))->bulkInsertQuery();
        $insert
            ->properties([
                ['code' => 'a'],
                ['code' => 'b'],
            ])
            ->onConflictDoNothing(['code'])
            ->returning(['code']);
        $this->assertSame([['code' => 'b']], $executor->build($insert)->executeReturning());
        $this->assertSame(2, (int)$pdo->query('SELECT count(*) FROM samples')->fetchColumn());
    }
}
