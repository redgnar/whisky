<?php

namespace Whisky\Executor;

use Whisky\Executor;
use Whisky\Scope;
use Whisky\Script;

class BasicExecutor implements Executor
{
    public function execute(Script $script, Scope $scope): void
    {
        $runTime = $script->getRunTime();
        $runTime->setScope($scope);
        $runTime->run();
    }
}
