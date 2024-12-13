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

    /**
     * プライマリキーのカラム名配列を取得
     * @return string[]
     */
    public function primaryKeys(): array
    {
        return [];
    }

    /**
     * プロパティ配列の返却
     * @return array<string, string|int|bool|float|RawExpr|null>
     */
    public function properties(): array
    {
        $properties = get_object_vars($this);
        foreach ($properties as $ky => $vl)
        {
            if (true === is_null($vl))
            {
                unset($properties[$ky]);
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
        $properties = $this->properties();
        foreach ($properties as $ky => $vl)
        {
            if (true === is_null($vl))
            {
                unset($properties[$ky]);
            }
        }
        return $properties;
    }

    /**
     * INSERT時に必要なカラム情報を補完する
     * @param string|null $timestamp
     */
    public function completeForCreate(string|null $timestamp = null): void
    {
        // タイムスタンプ設定
        $timestamp = $timestamp ?? Dates::now()->format('Y-m-d H:i:s');
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
        $timestamp = $timestamp ?? Dates::now()->format('Y-m-d H:i:s');
        $this->updated_at = $timestamp;
        $this->rev = new RawExpr('rev + 1');
    }
}
