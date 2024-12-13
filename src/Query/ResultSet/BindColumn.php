<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query\ResultSet;

/**
 * bindColumnのデフォルト実装
 */
trait BindColumn
{
    /**
     * @return $this
     */
    public function bindColumn(): self
    {
        return $this;
    }
}
