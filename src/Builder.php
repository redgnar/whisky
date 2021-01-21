<?php

declare(strict_types=1);

namespace Whisky;


interface Builder
{
    public function build(string $code): Script;
}
