<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Query\ResultSet;

/**
 * 結果セット(件数)
 */
class ResultCount implements ResultClass
{
    use BindColumn;

    public int $count = 0;
}
