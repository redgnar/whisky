<?php

namespace Whisky\Executor;

use Whisky\Executor;
use Whisky\RunError;
use Whisky\Scope;
use Whisky\Script;

class BasicExecutor implements Executor
{
    public function execute(Script $script, Scope $scope): void
    {
        $runTime = $script->getRunTime();
        $runTime->setScope($scope);
        try {
            $runTime->run();
        } catch (\Exception $e) {
            throw new RunError($e->getMessage());
        }
    }
}
