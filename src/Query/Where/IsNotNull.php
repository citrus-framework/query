<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2026, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query\Where;

/**
 * Where Part Is Not Null
 */
class IsNotNull implements Expression
{
    /**
     * constructor.
     * @param string $arg1
     */
    public function __construct(
        protected string $arg1,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function query(): string
    {
        return sprintf(
            '%s %s',
            $this->arg1,
            $this->operator(),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function operator(): string
    {
        return 'IS NOT NULL';
    }

    /**
     * {@inheritDoc}
     */
    public function parameters(): array
    {
        return [];
    }
}
