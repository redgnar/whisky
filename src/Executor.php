<?php

declare(strict_types=1);

namespace Whisky;


interface Executor
{
    public function execute(Script $script, Scope $scope): void;
}
