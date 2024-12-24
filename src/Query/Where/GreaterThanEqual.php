<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query\Where;

/**
 * Where Part Greater Than Equal
 */
class GreaterThanEqual implements Expression
{
    /**
     * constructor.
     * @param string $arg1
     * @param string $arg2
     */
    public function __construct(
        protected string $arg1,
        protected string $arg2,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function query(): string
    {
        return sprintf(
            '%s %s ?',
            $this->arg1,
            $this->operator(),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function operator(): string
    {
        return '>=';
    }

    /**
     * {@inheritDoc}
     */
    public function parameters(): array
    {
        return [$this->arg2];
    }
}
