<?php

declare(strict_types=1);

namespace Whisky;

interface Builder
{
    public function addExtension(Extension $extension): void;

    public function build(string $code, Scope $functions): Script;
}
