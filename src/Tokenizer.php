<?php

declare(strict_types=1);

namespace Whisky;

use Whisky\Tokenizer\TokenizedCode;

interface Tokenizer
{
    public function tokenize(string $code): TokenizedCode;
}
