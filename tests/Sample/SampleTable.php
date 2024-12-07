<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2024, CitrusQuery. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test\Sample;

use Citrus\Database\Columns;

/**
 * Select Query Test
 */
class SampleTable extends Columns
{
    public string $sample_id;
    public string $name;
    public string $description;
}