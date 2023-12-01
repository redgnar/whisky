<?php

declare(strict_types=1);

namespace Whisky;

interface Script
{
    public function getCode(): string;

    public function getCodeRunner(): \Closure;
}
