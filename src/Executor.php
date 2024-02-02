<?php

declare(strict_types=1);

namespace Whisky;

interface Executor
{
    /**
     * @return mixed
     */
    public function execute(Script $script, Scope $variables);
}
