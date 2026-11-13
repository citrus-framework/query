<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2026, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query;

/**
 * Returning
 */
trait Returning
{
    /** @var string[] */
    protected array $returnings = [];

    /**
     * Returning
     * 返った値は Executor::executeReturning() で読む
     * @param string[] $columns 返すカラム
     * @return $this
     */
    public function returning(array $columns): self
    {
        $this->returnings = $columns;
        return $this;
    }

    /**
     * クエリ生成
     * @return string
     */
    public function toReturningQuery(): string
    {
        return 'RETURNING ' . implode(', ', $this->returnings);
    }
}
