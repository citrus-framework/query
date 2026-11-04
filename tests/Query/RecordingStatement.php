<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test\Query;

use PDO;
use PDOStatement;

/**
 * bindValueの引数を記録するPDOStatement(テスト用)
 */
class RecordingStatement extends PDOStatement
{
    /** @var array<int, array{0: int|string, 1: mixed, 2: int}> bindValueの呼び出し記録 */
    public static array $calls = [];

    /**
     * @param int|string $param
     * @param mixed      $value
     * @param int        $type
     * @return bool
     */
    public function bindValue(int|string $param, mixed $value, int $type = PDO::PARAM_STR): bool
    {
        self::$calls[] = [$param, $value, $type];
        return parent::bindValue($param, $value, $type);
    }
}
