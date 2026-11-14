<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test\Query\ResultSet;

use Citrus\Query\RawExpr;
use Citrus\Query\ResultSet\Record;
use Citrus\Variable\Dates;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Record Test
 */
class RecordTest extends TestCase
{
    private const PATTERN = '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}[+-]\d{2}:?\d{2}$/';

    public function testCompleteForCreateWithTimezone(): void
    {
        $record = new Record();
        $record->completeForCreate();

        $this->assertMatchesRegularExpression(self::PATTERN, $record->created_at);
        $this->assertSame($record->created_at, $record->updated_at);
    }

    public function testCompleteForUpdateWithTimezone(): void
    {
        $record = new Record();
        $record->completeForUpdate();

        $this->assertMatchesRegularExpression(self::PATTERN, $record->updated_at);
        $this->assertInstanceOf(RawExpr::class, $record->rev);
    }

    public function testGivenTimestampIsUsedAsIs(): void
    {
        $record = new Record();
        $record->completeForCreate('2026-11-04 15:00:00');
        $this->assertSame('2026-11-04 15:00:00', $record->created_at);
        $this->assertSame('2026-11-04 15:00:00', $record->updated_at);

        $record->completeForUpdate('2026-11-05 01:02:03');
        $this->assertSame('2026-11-05 01:02:03', $record->updated_at);
    }

    /**
     * PHPの時刻帯ごとに、時差と瞬間が正しいことを確かめる
     * @param string $timezone 時刻帯
     * @param string $offset   期待する時差
     */
    #[DataProvider('timezoneProvider')]
    public function testTimestampMatchesTimezoneAndInstant(string $timezone, string $offset): void
    {
        $original = date_default_timezone_get();
        date_default_timezone_set($timezone);
        try
        {
            Dates::clear();
            $before = time();
            $record = new Record();
            $record->completeForCreate();
            $after = time();
            $record->completeForUpdate();
        }
        finally
        {
            date_default_timezone_set($original);
            Dates::clear();
        }

        foreach ([$record->created_at, $record->updated_at] as $value)
        {
            $this->assertMatchesRegularExpression('/' . preg_quote($offset, '/') . '$/', $value);
            $instant = (new DateTimeImmutable($value))->getTimestamp();
            $this->assertGreaterThanOrEqual($before - 1, $instant);
            $this->assertLessThanOrEqual($after + 5, $instant);
        }
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function timezoneProvider(): array
    {
        return [
            'Asia/Tokyo' => ['Asia/Tokyo', '+0900'],
            'UTC' => ['UTC', '+0000'],
        ];
    }
}
