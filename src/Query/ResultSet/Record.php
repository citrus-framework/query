<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query\ResultSet;

use Citrus\Query\RawExpr;
use Citrus\Variable\Binders;
use Citrus\Variable\Dates;

/**
 * テーブルの1レコードを表す
 */
class Record extends \stdClass implements ResultClass
{
    use Binders;
    use BindColumn;

    /** @var int|RawExpr|null status */
    public int|RawExpr|null $status = 0;

    /** @var string|RawExpr|null created_at */
    public string|RawExpr|null $created_at = null;

    /** @var string|RawExpr|null updated_at */
    public string|RawExpr|null $updated_at = null;

    /** @var int|RawExpr|null rowid */
    public int|RawExpr|null $rowid = null;

    /** @var int|RawExpr|null rev */
    public int|RawExpr|null $rev = null;

    /** @var array<string, string> setNull() で覚えた、null で保存するカラム名 */
    protected array $nullColumns = [];

    /**
     * プライマリキーのカラム名配列を取得
     * @return string[]
     */
    public function primaryKeys(): array
    {
        return [];
    }

    /**
     * 指定したプロパティに null を入れ、null で保存するカラムとして覚えておく
     * @param string ...$columns
     */
    public function setNull(string ...$columns): void
    {
        foreach ($columns as $column)
        {
            $this->{$column} = null;
            $this->nullColumns[$column] = $column;
        }
    }

    /**
     * プロパティ配列の返却
     * null でないプロパティに加えて、setNull() で覚えたカラムのうち今も null のものを、null として返す
     * @return array<string, string|int|bool|float|RawExpr|null>
     */
    public function properties(): array
    {
        $properties = get_object_vars($this);
        // 覚えておくための入れ物は、結果に入れない
        unset($properties['nullColumns']);
        $properties = array_filter($properties, static fn (mixed $vl): bool => $vl !== null);

        foreach ($this->nullColumns as $column)
        {
            // 入れ直されたカラムは、その値のまま返す
            if (array_key_exists($column, $properties) === false)
            {
                $properties[$column] = null;
            }
        }
        return $properties;
    }

    /**
     * null以外が設定しているプロパティ配列の返却
     * @return array<string, string|int|bool|float|RawExpr>
     */
    public function nonnullProperties(): array
    {
        return array_filter($this->properties(), static fn (mixed $vl): bool => $vl !== null);
    }

    /**
     * INSERT時に必要なカラム情報を補完する
     * @param string|null $timestamp
     */
    public function completeForCreate(string|null $timestamp = null): void
    {
        // タイムスタンプ設定
        $timestamp = $timestamp ?? Dates::now()->formatTimestampWithTimezone();
        $this->created_at = $timestamp;
        $this->updated_at = $timestamp;
    }

    /**
     * UPDATE時に必要なカラム情報を補完する
     * @param string|null $timestamp
     */
    public function completeForUpdate(string|null $timestamp = null): void
    {
        // タイムスタンプ設定
        $timestamp = $timestamp ?? Dates::now()->formatTimestampWithTimezone();
        $this->updated_at = $timestamp;
        $this->rev = new RawExpr('rev + 1');
    }
}
